<?php
/**
 * Traitement du formulaire de devis.
 * - validation côté serveur
 * - anti-spam : jeton CSRF, champ piège (honeypot), délai minimal, limite de fréquence
 * - envoi par mail() + sauvegarde dans storage/demandes.csv (filet de sécurité si le mail échoue)
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
    // Pré-sélection depuis un lien (ex. contact.php?service=villa-songon)
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

    if ($errors) {
        $_SESSION['form_time'] = time() - 10; // ne pas pénaliser une 2e tentative
        return ['errors' => $errors, 'old' => $old];
    }

    // Un robot reçoit une fausse confirmation, rien n'est envoyé.
    if (!$isBot) {
        devis_save($old);
        devis_mail($old);
        $_SESSION['last_sent'] = time();
    }

    $_SESSION['devis_ok'] = SERVICES[$old['service']];
    unset($_SESSION['csrf']);
    header('Location: contact.php?envoye=1#formulaire', true, 303);
    exit;
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
        $d['lieu'], $d['budget'], $d['msg'], $_SERVER['REMOTE_ADDR'] ?? '',
    ]), ';');
    fclose($fh);
}

function devis_mail(array $d): bool
{
    $oneLine = static fn(string $v): string => trim(preg_replace('/[\r\n]+/', ' ', $v));

    $subject = 'Demande de devis — ' . SERVICES[$d['service']] . ' — ' . $oneLine($d['nom']);
    $body = "Nouvelle demande depuis le site " . SITE['domain'] . "\n\n"
          . "Métier     : " . SERVICES[$d['service']] . "\n"
          . "Nom        : " . $oneLine($d['nom']) . "\n"
          . "Téléphone  : " . $oneLine($d['tel']) . "\n"
          . "E-mail     : " . ($d['mail'] ?: '—') . "\n"
          . "Lieu       : " . ($oneLine($d['lieu']) ?: '—') . "\n"
          . "Budget     : " . $d['budget'] . "\n\n"
          . "Message :\n" . ($d['msg'] ?: '—') . "\n\n"
          . "-- \nEnvoyé le " . date('d/m/Y à H:i');

    $headers = [
        'From: ' . SITE['legal'] . ' <no-reply@' . SITE['domain'] . '>',
        'Content-Type: text/plain; charset=UTF-8',
        'Content-Transfer-Encoding: 8bit',
        'X-Mailer: PHP',
    ];
    if ($d['mail'] !== '') {
        $headers[] = 'Reply-To: ' . $d['mail']; // déjà validée par FILTER_VALIDATE_EMAIL
    }

    $ok = @mail(
        CONTACT_TO,
        '=?UTF-8?B?' . base64_encode($subject) . '?=',
        $body,
        implode("\r\n", $headers)
    );
    if (!$ok) {
        error_log('[devis] mail() a échoué — la demande est conservée dans storage/demandes.csv');
    }
    return $ok;
}
