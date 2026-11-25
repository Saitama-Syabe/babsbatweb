<?php
/**
 * Traitement du formulaire de devis.
 * - validation côté serveur
 * - anti-spam : jeton CSRF, champ piège (honeypot), délai minimal, 1 envoi/min par session,
 *   limite par IP (RATE_LIMIT_PER_HOUR, 5 par défaut), filtre de contenu (liens, mots-clés, écritures)
 * - envoi SMTP + sauvegarde dans storage/demandes.csv (filet de sécurité si le mail échoue)
 *
 * Retourne ['errors' => [...], 'old' => [...]] ; en cas de succès, redirige (POST → GET).
 */
require_once __DIR__ . '/config.php';

function devis_handle(): array
{
    $old = [
        'service' => 'construction',
        'nom' => '', 'tel' => '', 'mail' => '', 'lieu' => '',
        'budget' => BUDGETS[0], 'msg' => '',
    ];
    // Pré-sélection depuis un lien (ex. contact?service=villa-songon)
    if (isset($_GET['service']) && isset(SERVICES[$_GET['service']])) {
        $old['service'] = $_GET['service'];
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $_SESSION['form_time'] = time();
        return ['errors' => [], 'old' => $old];
    }

    $errors = [];
    foreach (array_keys($old) as $k) {
        $old[$k] = trim((string) ($_POST[$k] ?? ''));
    }

    // --- Anti-spam ---
    $token = (string) ($_POST['csrf'] ?? '');
    if (!hash_equals($_SESSION['csrf'] ?? '', $token)) {
        $errors['global'] = 'Votre session a expiré. Merci de renvoyer le formulaire.';
    }
    $isBot = ($_POST['site_web'] ?? '') !== ''                               // champ piège rempli
          || (time() - (int) ($_SESSION['form_time'] ?? 0)) < 3;             // envoyé trop vite
    if (!empty($_SESSION['last_sent']) && time() - $_SESSION['last_sent'] < 60) {
        $errors['global'] = 'Une demande vient déjà d\'être envoyée. Merci de patienter une minute.';
    }

    // --- Validation ---
    if (!isset(SERVICES[$old['service']])) {
        $errors['service'] = 'Choisissez un métier.';
    }
    if (mb_strlen($old['nom']) < 2 || mb_strlen($old['nom']) > 100) {
        $errors['nom'] = 'Indiquez votre nom et prénom.';
    }
    $digits = preg_replace('/\D+/', '', $old['tel']);
    if (strlen($digits) < 8 || strlen($digits) > 15) {
        $errors['tel'] = 'Indiquez un numéro de téléphone valide.';
    }
    if ($old['mail'] !== '' && !filter_var($old['mail'], FILTER_VALIDATE_EMAIL)) {
        $errors['mail'] = 'Cette adresse e-mail ne semble pas valide.';
    }
    if (mb_strlen($old['lieu']) > 150) {
        $errors['lieu'] = '150 caractères maximum.';
    }
    if (!in_array($old['budget'], BUDGETS, true)) {
        $old['budget'] = BUDGETS[0];
    }
    if (mb_strlen($old['msg']) > 3000) {
        $errors['msg'] = '3000 caractères maximum.';
    }

    // --- Filtre de contenu ---
    $spamReason = null;
    if (!$errors) {
        [$spamMsg, $isSpam] = spam_check($old);
        if ($spamMsg !== null) {
            $field = str_contains($spamMsg, 'message') ? 'msg' : (str_contains($spamMsg, 'nom') ? 'nom' : 'global');
            $errors[$field] = $spamMsg;
        } elseif ($isSpam) {
            $spamReason = 'contenu';
        }
    }

    // --- reCAPTCHA v3 ---
    if (!$errors && !$isBot && $spamReason === null && RECAPTCHA_ON) {
        [$human, $why] = recaptcha_verify((string) ($_POST['g-recaptcha-response'] ?? ''));
        if (!$human) {
            spam_log($old, 'reCAPTCHA : ' . $why);
            $errors['global'] = 'Nous n\'avons pas pu vérifier votre demande. Merci de réessayer, '
                . 'ou appelez-nous au ' . PHONES['main'][0] . '.';
        }
    }

    // --- Limite par adresse IP ---
    if (!$errors && !$isBot && $spamReason === null && !rate_limit_ok()) {
        $errors['global'] = 'Trop de demandes ont été envoyées depuis votre connexion. Merci de réessayer dans une heure, ou appelez-nous directement.';
    }

    if ($errors) {
        $_SESSION['form_time'] = time() - 10; // ne pas pénaliser une 2e tentative
        return ['errors' => $errors, 'old' => $old];
    }

    // Robot ou spam : fausse confirmation, rien n'est envoyé (le spam est journalisé pour contrôle).
    if ($isBot) {
        spam_log($old, ($_POST['site_web'] ?? '') !== '' ? 'champ piège' : 'trop rapide');
    } elseif ($spamReason !== null) {
        spam_log($old, $spamReason);
    } else {
        rate_limit_ok(true);
        devis_save($old);
        devis_mail($old);
        $_SESSION['last_sent'] = time();
    }

    $_SESSION['devis_ok'] = SERVICES[$old['service']];
    unset($_SESSION['csrf']);
    header('Location: contact?envoye=1#formulaire', true, 303);
    exit;
}

/**
 * IP du visiteur.
 * Derrière le proxy Traefik de Dokploy, REMOTE_ADDR est l'IP (privée) du proxy et l'IP réelle est la
 * DERNIÈRE de X-Forwarded-For (celle ajoutée par le proxy). Les premières valeurs peuvent être
 * falsifiées par le client : on ne les utilise jamais.
 */
function client_ip(): string
{
    $remote = (string) ($_SERVER['REMOTE_ADDR'] ?? '');
    $fromProxy = $remote !== '' && !filter_var($remote, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
    if ($fromProxy && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $parts = array_map('trim', explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']));
        $last  = end($parts);
        if (filter_var($last, FILTER_VALIDATE_IP)) {
            return $last;
        }
    }
    return $remote;
}

/**
 * Limite par adresse IP : au plus RATE_LIMIT_PER_HOUR demandes par heure glissante.
 * Les IP sont stockées hachées (pas d'IP en clair) dans storage/ratelimit.json.
 * $record = true enregistre une demande ; false ne fait que vérifier.
 */
function rate_limit_ok(bool $record = false): bool
{
    $max  = max(1, (int) env('RATE_LIMIT_PER_HOUR', '5'));
    $dir  = dirname(__DIR__) . '/storage';
    if (!is_dir($dir)) {
        @mkdir($dir, 0750, true);
    }
    $fh = @fopen($dir . '/ratelimit.json', 'c+');
    if (!$fh) {
        return true; // en cas de souci disque, on ne bloque pas les vrais clients
    }
    flock($fh, LOCK_EX);
    $data = json_decode(stream_get_contents($fh) ?: '{}', true) ?: [];
    $now  = time();
    // Purge des entrées de plus d'une heure
    foreach ($data as $k => $times) {
        $data[$k] = array_values(array_filter((array) $times, fn($t) => $t > $now - 3600));
        if (!$data[$k]) {
            unset($data[$k]);
        }
    }
    $key = hash('sha256', client_ip() . '|' . __DIR__);
    $ok  = count($data[$key] ?? []) < $max;
    if ($ok && $record) {
        $data[$key][] = $now;
    }
    ftruncate($fh, 0);
    rewind($fh);
    fwrite($fh, json_encode($data));
    fflush($fh);
    flock($fh, LOCK_UN);
    fclose($fh);
    return $ok;
}

/**
 * Filtre de contenu.
 * Retourne [message d'erreur affiché ou null, true si c'est manifestement du spam (rejet silencieux)].
 */
function spam_check(array $d): array
{
    $urlRe = '~(https?://|www\.|\b[a-z0-9-]+\.(com|net|org|info|biz|ru|xyz|top|site|online|shop|io|co)\b)~i';
    // Les adresses e-mail ne comptent pas comme des liens
    $count = static fn(string $s): int => preg_match_all($urlRe, preg_replace('/\S+@\S+/', ' ', $s));

    // Liens : aucun dans les champs courts, au plus 1 dans le message (ex. lien Google Maps)
    if ($count($d['nom']) || $count($d['lieu']) || $count($d['tel'])) {
        return ['Les liens web ne sont pas acceptés dans ces champs.', false];
    }
    if ($count($d['msg']) > 1) {
        return ['Merci de ne pas mettre plus d\'un lien dans votre message.', false];
    }

    $all = mb_strtolower($d['nom'] . ' ' . $d['lieu'] . ' ' . $d['msg']);

    // Écritures étrangères au public visé (cyrillique, chinois/japonais/coréen) : typique du spam
    if (preg_match('/[\p{Cyrillic}\p{Han}\p{Hiragana}\p{Katakana}\p{Hangul}]/u', $all)) {
        return [null, true];
    }
    // Mots-clés de spam courants
    $words = ['viagra', 'cialis', 'casino', 'porn', 'escort', 'crypto', 'bitcoin', 'forex',
              'backlink', 'seo service', 'seo services', 'rank your website', 'guest post', 'loan offer',
              'click here', 'cliquez ici', 'earn money', 'make money', 'work from home', 'telegram',
              'whatsapp me', 'investment opportunity', 'unsubscribe'];
    foreach ($words as $w) {
        if (str_contains($all, $w)) {
            return [null, true];
        }
    }
    // Balises HTML / BBCode dans un formulaire texte
    if (preg_match('~<a\s|\[url[=\]]|</?(script|iframe)~i', $d['msg'] . $d['nom'])) {
        return [null, true];
    }
    // Nom invraisemblable (chiffres, ou une seule « suite » sans voyelle)
    if (preg_match('/\d{3,}/', $d['nom'])) {
        return ['Indiquez votre nom et prénom.', false];
    }
    return [null, false];
}

/**
 * Vérifie le jeton reCAPTCHA v3 auprès de Google.
 * Retourne [true si humain, raison du refus].
 * Si Google est injoignable, on laisse passer (les autres protections restent actives)
 * plutôt que de bloquer de vrais clients.
 */
function recaptcha_verify(string $token): array
{
    if ($token === '') {
        return [false, 'jeton absent (JavaScript désactivé ou robot)'];
    }
    if (RECAPTCHA_MODE === 'enterprise') {
        return recaptcha_verify_enterprise($token);
    }
    $ctx = stream_context_create(['http' => [
        'method'  => 'POST',
        'header'  => "Content-Type: application/x-www-form-urlencoded\r\n",
        'content' => http_build_query([
            'secret'   => RECAPTCHA['secret'],
            'response' => $token,
            'remoteip' => client_ip(),
        ]),
        'timeout' => 6,
    ]]);
    $raw = @file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $ctx);
    if ($raw === false) {
        error_log('[devis] reCAPTCHA injoignable, demande acceptée sans vérification');
        return [true, ''];
    }
    $r = json_decode($raw, true) ?: [];
    if (empty($r['success'])) {
        return [false, 'jeton invalide (' . implode(',', (array) ($r['error-codes'] ?? [])) . ')'];
    }
    if (($r['action'] ?? '') !== RECAPTCHA['action']) {
        return [false, 'action inattendue « ' . ($r['action'] ?? '') . ' »'];
    }
    $score = (float) ($r['score'] ?? 0);
    if ($score < RECAPTCHA['min_score']) {
        return [false, 'score ' . $score . ' < ' . RECAPTCHA['min_score']];
    }
    return [true, ''];
}

/**
 * reCAPTCHA Enterprise : création d'une « évaluation » via l'API Google Cloud.
 * https://cloud.google.com/recaptcha/docs/create-assessment-website
 */
function recaptcha_verify_enterprise(string $token): array
{
    $url = 'https://recaptchaenterprise.googleapis.com/v1/projects/'
         . rawurlencode(RECAPTCHA['project_id']) . '/assessments?key=' . rawurlencode(RECAPTCHA['api_key']);
    $body = json_encode(['event' => [
        'token'          => $token,
        'siteKey'        => RECAPTCHA['site_key'],
        'expectedAction' => RECAPTCHA['action'],
        'userIpAddress'  => client_ip(),
        'userAgent'      => (string) ($_SERVER['HTTP_USER_AGENT'] ?? ''),
    ]]);
    $ctx = stream_context_create(['http' => [
        'method'        => 'POST',
        'header'        => "Content-Type: application/json\r\n",
        'content'       => $body,
        'timeout'       => 6,
        'ignore_errors' => true, // pour lire le message d'erreur JSON en cas de 4xx
    ]]);
    $raw = @file_get_contents($url, false, $ctx);
    if ($raw === false) {
        error_log('[devis] reCAPTCHA Enterprise injoignable, demande acceptée sans vérification');
        return [true, ''];
    }
    $r = json_decode($raw, true) ?: [];
    if (isset($r['error'])) {
        // Erreur de configuration (clé API, projet, API non activée) : on ne bloque pas les clients
        error_log('[devis] reCAPTCHA Enterprise — erreur de configuration : ' . ($r['error']['message'] ?? $raw));
        return [true, ''];
    }
    $tp = $r['tokenProperties'] ?? [];
    if (empty($tp['valid'])) {
        return [false, 'jeton invalide (' . ($tp['invalidReason'] ?? '?') . ')'];
    }
    if (($tp['action'] ?? '') !== RECAPTCHA['action']) {
        return [false, 'action inattendue « ' . ($tp['action'] ?? '') . ' »'];
    }
    $score = (float) ($r['riskAnalysis']['score'] ?? 0);
    if ($score < RECAPTCHA['min_score']) {
        return [false, 'score ' . $score . ' < ' . RECAPTCHA['min_score']];
    }
    return [true, ''];
}

/** Conserve les demandes rejetées comme spam (pour repérer d'éventuels faux positifs). */
function spam_log(array $d, string $reason): void
{
    $file = dirname(__DIR__) . '/storage/spam.csv';
    $fh = @fopen($file, 'ab');
    if (!$fh) {
        return;
    }
    $safe = static fn(string $v): string => preg_match('/^[=+\-@]/', $v) ? "'" . $v : $v;
    fputcsv($fh, array_map($safe, [date('Y-m-d H:i:s'), $reason, $d['nom'], $d['tel'], $d['mail'], $d['lieu'], mb_substr($d['msg'], 0, 500)]), ';');
    fclose($fh);
}

function devis_save(array $d): void
{
    $dir = dirname(__DIR__) . '/storage';
    if (!is_dir($dir)) {
        @mkdir($dir, 0750, true);
    }
    $file = $dir . '/demandes.csv';
    $new  = !file_exists($file);
    $fh   = @fopen($file, 'ab');
    if (!$fh) {
        error_log('[devis] impossible d\'écrire ' . $file);
        return;
    }
    if ($new) {
        fwrite($fh, "\xEF\xBB\xBF"); // BOM pour Excel
        fputcsv($fh, ['date', 'metier', 'nom', 'telephone', 'email', 'lieu', 'budget', 'message', 'ip'], ';');
    }
    // Préfixe les valeurs commençant par = + - @ pour éviter l'injection de formules dans Excel
    $safe = static fn(string $v): string => preg_match('/^[=+\-@]/', $v) ? "'" . $v : $v;
    fputcsv($fh, array_map($safe, [
        date('Y-m-d H:i:s'), SERVICES[$d['service']], $d['nom'], $d['tel'], $d['mail'],
        $d['lieu'], $d['budget'], $d['msg'], client_ip(),
    ]), ';');
    fclose($fh);
}

function devis_mail(array $d): bool
{
    require_once __DIR__ . '/mail-devis.php';

    $when      = date('d/m/Y') . ' à ' . date('H:i');
    $subject   = devis_mail_subject($d);
    $text      = devis_mail_text($d, $when);
    $replyTo   = $d['mail']; // déjà validée par FILTER_VALIDATE_EMAIL (ou vide)
    $replyName = trim(preg_replace('/[\r\n]+/', ' ', $d['nom']));

    return SMTP['host'] !== ''
        ? devis_send_smtp($subject, $text, $replyTo, $replyName, devis_mail_html($d, $when))
        : devis_send_phpmail($subject, $text, $replyTo);
}

/**
 * Prépare un PHPMailer configuré pour le SMTP (utilisé par le site et par bin/smtp-test.php).
 * Délais courts : si le serveur SMTP est injoignable, le visiteur n'attend pas plus de ~10 s.
 */
function smtp_mailer(): PHPMailer\PHPMailer\PHPMailer
{
    require_once dirname(__DIR__) . '/lib/PHPMailer/Exception.php';
    require_once dirname(__DIR__) . '/lib/PHPMailer/PHPMailer.php';
    require_once dirname(__DIR__) . '/lib/PHPMailer/SMTP.php';

    $m = new PHPMailer\PHPMailer\PHPMailer(true);
    $m->isSMTP();
    $m->Host       = SMTP['host'];
    $m->Port       = SMTP['port'];
    $m->SMTPAuth   = SMTP['user'] !== '';
    $m->Username   = SMTP['user'];
    $m->Password   = SMTP['pass'];
    $m->SMTPSecure = match (SMTP['secure']) {
        'ssl'   => PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS,
        'none'  => '',
        default => PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS,
    };
    if (SMTP['secure'] === 'none') {
        $m->SMTPAutoTLS = false;
    }
    $m->Timeout = 8;                    // connexion au serveur
    $m->getSMTPInstance()->Timelimit = 10; // attente de chaque réponse du serveur
    $m->CharSet = PHPMailer\PHPMailer\PHPMailer::CHARSET_UTF8;
    $m->setFrom(SMTP['from'], SMTP['from_name']);
    return $m;
}

/**
 * Envoi via SMTP authentifié (PHPMailer).
 * Avec $html : e-mail HTML + version texte, logo joint en image intégrée (cid:logo).
 */
function devis_send_smtp(string $subject, string $text, string $replyTo, string $replyName, string $html = ''): bool
{
    $m = null;
    try {
        $m = smtp_mailer();
        $m->addAddress(CONTACT_TO);
        if ($replyTo !== '') {
            $m->addReplyTo($replyTo, $replyName);
        }
        $m->Subject = $subject;
        if ($html !== '') {
            $m->isHTML(true);
            $m->Body    = $html;
            $m->AltBody = $text;
            $logo = dirname(__DIR__) . '/img/mail-logo.png';
            if (is_file($logo)) {
                $m->addEmbeddedImage($logo, 'logo', 'logo.png', 'base64', 'image/png');
            }
        } else {
            $m->isHTML(false);
            $m->Body = $text;
        }

        return $m->send();
    } catch (Throwable $e) {
        error_log('[devis] SMTP a échoué (' . SMTP['host'] . ':' . SMTP['port'] . ' ' . SMTP['secure'] . ') : '
            . ($m ? $m->ErrorInfo : $e->getMessage())
            . '. Demande conservée dans storage/demandes.csv. Diagnostic : php bin/smtp-test.php');
        return false;
    }
}

/** Repli : fonction mail() de PHP (rarement disponible dans un conteneur). */
function devis_send_phpmail(string $subject, string $body, string $replyTo): bool
{
    $headers = [
        'From: ' . SMTP['from_name'] . ' <' . SMTP['from'] . '>',
        'Content-Type: text/plain; charset=UTF-8',
        'Content-Transfer-Encoding: 8bit',
    ];
    if ($replyTo !== '') {
        $headers[] = 'Reply-To: ' . $replyTo;
    }
    $ok = @mail(CONTACT_TO, '=?UTF-8?B?' . base64_encode($subject) . '?=', $body, implode("\r\n", $headers));
    if (!$ok) {
        error_log('[devis] mail() a échoué — la demande est conservée dans storage/demandes.csv');
    }
    return $ok;
}
