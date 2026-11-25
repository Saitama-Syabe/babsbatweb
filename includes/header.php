<?php
/**
 * En-tête commun.
 * Variables attendues avant l'include :
 *   $page        nom du fichier courant (ex. 'index.php')
 *   $title       titre de la page
 *   $description meta description
 */
require_once __DIR__ . '/config.php';

$page        = $page ?? 'index.php';
$title       = $title ?? SITE['name'];
$description = $description ?? SITE['slogan'];
$canonical   = SITE['url'] . '/' . ltrim(page_url($page), './');
$noindex     = $noindex ?? false;
// Dossier racine du site ('/' en production) : sert de base à tous les liens relatifs
$base        = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/') . '/';
$self        = page_url($page); // ancres internes (#…) : à préfixer par la page courante
?>
<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<base href="<?= e($base) ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php if ($noindex): ?><meta name="robots" content="noindex"><?php endif; ?>
<title><?= e($title) ?></title>
<meta name="description" content="<?= e($description) ?>">
<link rel="canonical" href="<?= e($canonical) ?>">
<meta name="theme-color" content="#B5451B">
<meta property="og:type" content="website">
<meta property="og:locale" content="fr_FR">
<meta property="og:site_name" content="<?= e(SITE['legal']) ?>">
<meta property="og:title" content="<?= e($title) ?>">
<meta property="og:description" content="<?= e($description) ?>">
<meta property="og:url" content="<?= e($canonical) ?>">
<meta property="og:image" content="<?= e(SITE['url']) ?>/img/render-carport.jpg">
<link rel="icon" href="img/logo.svg" type="image/svg+xml">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Anton&amp;family=Barlow:wght@400;500;600;700&amp;family=Barlow+Condensed:wght@600;700&amp;display=swap" rel="stylesheet">
<link rel="stylesheet" href="css/style.css?v=11">
<script>document.documentElement.classList.add('js');</script>
</head>
<body>
<a class="skip-link" href="<?= e($self) ?>#contenu">Aller au contenu</a>

<header class="site-header" data-header>
  <div class="wrap site-header__inner">
    <a href="./" class="brand" aria-label="<?= e(SITE['legal']) ?> — accueil">
      <?php include __DIR__ . '/logo.svg.php'; ?>
      <span class="brand__text">
        <span class="dsp brand__name">BAB'S-BAT <small>SARL</small></span>
        <span class="mono brand__tag"><?= e(SITE['slogan']) ?></span>
      </span>
    </a>
    <button type="button" class="nav-toggle mono" data-nav-toggle aria-expanded="false" aria-controls="menu-principal">
      <span class="nav-toggle__icon" aria-hidden="true"><i></i><i></i><i></i></span>
      <span data-nav-label>Menu</span>
    </button>
    <nav id="menu-principal" aria-label="Navigation principale" class="mono nav">
      <?php foreach (NAV as $file => $label): ?>
        <a href="<?= page_url($file) ?>"<?= $file === $page ? ' aria-current="page"' : '' ?>><?= e($label) ?></a>
      <?php endforeach; ?>
      <a href="contact" class="nav__cta"<?= $page === 'contact.php' ? ' aria-current="page"' : '' ?>>Devis gratuit →</a>
    </nav>
  </div>
</header>

<main id="contenu">
