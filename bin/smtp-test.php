<?php
/**
 * Diagnostic SMTP — à lancer DANS le conteneur (Dokploy → Docker → Terminal) :
 *
 *   php bin/smtp-test.php                 vérifie la configuration et la connexion
 *   php bin/smtp-test.php --send          envoie en plus un e-mail de test à CONTACT_TO
 *
 * N'affiche jamais le mot de passe. Interdit au web (.htaccess + PHP_SAPI).
 */
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit;
}
require dirname(__DIR__) . '/includes/devis.php';

$ok   = static fn(string $s) => print("  \033[32m✔\033[0m $s\n");
$ko   = static fn(string $s) => print("  \033[31m✘\033[0m $s\n");
$info = static fn(string $s) => print("    $s\n");

echo "\n== 1. Configuration\n";
foreach (['host', 'port', 'secure', 'user', 'from'] as $k) {
    $v = (string) SMTP[$k];
    $v === '' ? $ko("SMTP $k : vide") : $ok("SMTP $k : $v");
}
SMTP['pass'] === '' ? $ko('SMTP_PASS : vide') : $ok('SMTP_PASS : renseigné (' . strlen(SMTP['pass']) . ' caractères)');
$ok('CONTACT_TO : ' . CONTACT_TO);
if (SMTP['host'] === '') {
    $ko('SMTP_HOST est vide : rien à tester.');
    exit(1);
}
if (strcasecmp(SMTP['from'], SMTP['user']) !== 0 && SMTP['user'] !== '') {
    $info('Attention : MAIL_FROM est différent de SMTP_USER (souvent refusé par le serveur).');
}
$expected = ['587' => 'tls', '465' => 'ssl', '25' => 'tls', '2525' => 'tls'];
if (isset($expected[(string) SMTP['port']]) && $expected[(string) SMTP['port']] !== SMTP['secure']) {
    $info('Attention : le port ' . SMTP['port'] . ' s\'utilise normalement avec SMTP_SECURE=' . $expected[(string) SMTP['port']] . '.');
}

echo "\n== 2. Résolution DNS de " . SMTP['host'] . "\n";
$ips = gethostbynamel(SMTP['host']) ?: [];
$ips ? $ok('Adresse(s) : ' . implode(', ', $ips)) : $ko('Nom introuvable : SMTP_HOST est probablement mal écrit.');

echo "\n== 3. Ports sortants (le serveur/VPS les laisse-t-il passer ?)\n";
$reachable = [];
foreach (array_unique([SMTP['port'], 587, 465, 2525, 25]) as $port) {
    $t = microtime(true);
    $fp = @fsockopen(SMTP['host'], (int) $port, $errno, $errstr, 5);
    $ms = (int) ((microtime(true) - $t) * 1000);
    if ($fp) {
        fclose($fp);
        $reachable[] = $port;
        $ok("port $port : ouvert ($ms ms)" . ($port == SMTP['port'] ? '  ← port configuré' : ''));
    } else {
        $ko("port $port : bloqué ou fermé ($errstr)" . ($port == SMTP['port'] ? '  ← port configuré' : ''));
    }
}
if (!$reachable) {
    $info('Aucun port joignable : l\'hébergeur du serveur bloque très probablement le SMTP sortant.');
    $info('Solutions : demander le déblocage au support du VPS, ou utiliser un service sur le port 2525 / une API HTTP.');
} elseif (!in_array(SMTP['port'], $reachable)) {
    $info('Le port configuré est bloqué, mais ' . implode(', ', $reachable) . ' répond : changer SMTP_PORT (et SMTP_SECURE).');
}

echo "\n== 4. Connexion et authentification SMTP\n";
try {
    $m = smtp_mailer();
    $m->SMTPDebug   = 2;
    $m->Debugoutput = static function ($str) {
        // masque l'identifiant/mot de passe encodés envoyés pendant AUTH
        if (preg_match('/^CLIENT -> SERVER: [A-Za-z0-9+\/=]{8,}\s*$/', trim($str))) {
            $str = "CLIENT -> SERVER: ******** (masqué)\n";
        }
        echo '    | ' . rtrim($str) . "\n";
    };
    if ($m->smtpConnect()) {
        $ok('Connexion et authentification réussies.');
        $m->smtpClose();
    }
} catch (Throwable $e) {
    $ko('Échec : ' . ((isset($m) && $m->ErrorInfo) ? $m->ErrorInfo : $e->getMessage()));
    exit(1);
}

if (in_array('--send', $argv, true)) {
    echo "\n== 5. Envoi d'un e-mail de test à " . CONTACT_TO . "\n";
    try {
        $m = smtp_mailer();
        $m->addAddress(CONTACT_TO);
        $m->Subject = 'Test SMTP — site ' . SITE['domain'];
        $m->Body    = "E-mail de test envoyé par bin/smtp-test.php le " . date('d/m/Y à H:i') . ".\nSi vous le lisez, l'envoi des devis fonctionne.";
        $m->send();
        $ok('E-mail envoyé. Vérifiez la boîte de réception (et les spams).');
    } catch (Throwable $e) {
        $ko('Échec de l\'envoi : ' . ((isset($m) && $m->ErrorInfo) ? $m->ErrorInfo : $e->getMessage()));
        exit(1);
    }
}
echo "\n";
