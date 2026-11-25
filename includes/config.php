<?php
/**
 * Informations de l'entreprise — modifier ici, cela se répercute sur tout le site.
 */

const SITE = [
    'name'     => "BAB'S-BAT",
    'legal'    => "BAB'S-BAT SARL",
    'slogan'   => 'Bâtissons ensemble les maisons de demain',
    'domain'   => 'babsbatsarl-ci.com',
    'url'      => 'https://babsbatsarl-ci.com',
    'email'    => 'bas@babsbatsarl-ci.com',
    'address'  => "Abidjan Cocody Riviera Faya, bd Mitterrand, face à l'église orthodoxe, au-dessus de la banque BOA",
    'po_box'   => 'BP 482 Bingerville',
    'manager'  => 'Aimé Stéphane BABO',
    'hours'    => ['Lundi – vendredi : 8 h – 17 h', 'Samedi : 9 h – 12 h'],
    'map_query'=> 'Riviera Faya, Boulevard Mitterrand, Abidjan',
];

/** Téléphones : [affichage, format international pour tel:] */
const PHONES = [
    'main'   => ['(+225) 07 88 37 37 37', '+2250788373737'],
    'mobile' => ['(+225) 07 07 90 08 90', '+2250707900890'],
    'fixe'   => ['(+225) 27 22 28 96 69', '+2252722289669'],
];

const WHATSAPP = 'https://wa.me/2250788373737';

/**
 * Lecture d'une variable d'environnement (Dokploy → onglet Environment).
 * En local (XAMPP), on peut créer includes/config.local.php qui retourne un tableau
 * ['SMTP_HOST' => '...', ...] — ce fichier est ignoré par git.
 */
function env(string $key, string $default = ''): string
{
    static $local = null;
    if ($local === null) {
        $file  = __DIR__ . '/config.local.php';
        $local = is_file($file) ? (array) require $file : [];
    }
    $v = getenv($key);
    if ($v === false || $v === '') {
        $v = $_ENV[$key] ?? $_SERVER[$key] ?? $local[$key] ?? $default;
    }
    return (string) $v;
}

/** Adresse qui reçoit les demandes de devis. */
define('CONTACT_TO', env('CONTACT_TO', 'bas@babsbatsarl-ci.com'));

/**
 * Normalise SMTP_SECURE et le met en cohérence avec le port :
 *  - 465 = chiffrement immédiat (ssl / SMTPS), 587 et 2525 = STARTTLS (tls)
 *  - accepte aussi true / 1 / yes / starttls / smtps / false / off…
 */
function smtp_secure(string $value, int $port): string
{
    $v = strtolower(trim($value));
    if (in_array($v, ['none', 'false', '0', 'off', 'no'], true)) {
        return 'none';
    }
    if ($port === 465) {
        return 'ssl';                // le port 465 n'accepte que le chiffrement immédiat
    }
    if (in_array($port, [587, 2525], true)) {
        return 'tls';                // ces ports n'acceptent que STARTTLS
    }
    return in_array($v, ['ssl', 'smtps'], true) ? 'ssl' : 'tls';
}

/** Envoi des e-mails par SMTP (si SMTP_HOST est vide, repli sur mail()). */
define('SMTP', [
    'host'      => env('SMTP_HOST'),
    'port'      => (int) env('SMTP_PORT', '587'),
    'user'      => env('SMTP_USER'),
    'pass'      => env('SMTP_PASS'),
    'secure'    => smtp_secure(env('SMTP_SECURE', 'tls'), (int) env('SMTP_PORT', '587')), // tls | ssl | none
    'from'      => env('MAIL_FROM', 'no-reply@babsbatsarl-ci.com'),
    'from_name' => env('MAIL_FROM_NAME', "Site BAB'S-BAT"),
]);

/**
 * Google reCAPTCHA (invisible, basé sur un score). Désactivé tant qu'il n'est pas configuré.
 *
 * Deux modes, choisis automatiquement selon les variables renseignées :
 *  - Enterprise (clé créée dans Google Cloud Console) :
 *      RECAPTCHA_SITE_KEY + RECAPTCHA_PROJECT_ID + RECAPTCHA_API_KEY
 *  - Classique v3 (clé créée sur google.com/recaptcha/admin) :
 *      RECAPTCHA_SITE_KEY + RECAPTCHA_SECRET
 */
define('RECAPTCHA', [
    'site_key'   => env('RECAPTCHA_SITE_KEY'),
    'secret'     => env('RECAPTCHA_SECRET'),
    'project_id' => env('RECAPTCHA_PROJECT_ID'),
    'api_key'    => env('RECAPTCHA_API_KEY'),
    'min_score'  => (float) env('RECAPTCHA_MIN_SCORE', '0.5'), // 0.0 = robot … 1.0 = humain
    'action'     => 'devis',
]);
define('RECAPTCHA_MODE', RECAPTCHA['site_key'] === '' ? ''
    : (RECAPTCHA['project_id'] !== '' && RECAPTCHA['api_key'] !== '' ? 'enterprise'
    : (RECAPTCHA['secret'] !== '' ? 'classic' : '')));
define('RECAPTCHA_ON', RECAPTCHA_MODE !== '');

/** Pages du menu principal : fichier => libellé */
const NAV = [
    'index.php'     => 'Accueil',
    'metiers.php'   => 'Métiers',
    'chantiers.php' => 'Chantiers',
];

/**
 * Vidéos de la page d'accueil (section « Sur le terrain »).
 * Fichiers hébergés sur MinIO (bucket en lecture publique).
 * 'fit' : cover (remplit le cadre) ou contain (vidéo entière, bandes noires).
 * Dossier modifiable via la variable d'environnement VIDEO_BASE_URL.
 */
define('VIDEO_BASE_URL', rtrim(env('VIDEO_BASE_URL', 'https://minio-files.kronyxtech.com/other.bucket/video'), '/'));

define('VIDEOS', [
    ['src' => VIDEO_BASE_URL . '/video-1.mp4', 'caption' => 'La latérite, matière première de nos briques',        'fit' => 'cover'],
    ['src' => VIDEO_BASE_URL . '/video-2.mp4', 'caption' => 'Des milliers de briques BTCS en séchage',             'fit' => 'contain'],
    ['src' => VIDEO_BASE_URL . '/video-3.mp4', 'caption' => 'Livraison de latérite sur le site de production',     'fit' => 'cover'],
    ['src' => VIDEO_BASE_URL . '/video-4.mp4', 'caption' => 'Les briques prêtes à partir sur le chantier',         'fit' => 'cover'],
]);

/** Les métiers proposés dans le formulaire de devis. */
const SERVICES = [
    'construction' => 'Construction',
    'villa-songon' => 'Villa à Songon',
    'lotissement'  => 'Lotissement',
    'location'     => 'Location-vente',
    'gestion'      => 'Gestion immobilière',
    'securite'     => 'Vidéosurveillance',
    'solaire'      => 'Solaire',
    'autre'        => 'Autre',
];

const BUDGETS = [
    'Je ne sais pas encore',
    'Moins de 30 millions FCFA',
    '30 à 75 millions FCFA',
    'Plus de 75 millions FCFA',
];

/**
 * URL propre d'une page (sans .php) : 'metiers.php' → 'metiers', 'index.php' → './'.
 * Les liens restent relatifs pour fonctionner aussi en sous-dossier (XAMPP).
 */
function page_url(string $file): string
{
    $slug = basename($file, '.php');
    return $slug === 'index' ? './' : $slug;
}

/** Échappement HTML court. */
function e(?string $s): string
{
    return htmlspecialchars((string) $s, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}
