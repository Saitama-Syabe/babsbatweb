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
$canonical   = SITE['url'] . '/' . ($page === 'index.php' ? '' : $page);
?>
<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
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
<link rel="stylesheet" href="css/style.css?v=3">
</head>
<body>
<a class="skip-link" href="#contenu">Aller au contenu</a>

<header class="site-header">
  <div class="wrap site-header__inner">
    <a href="index.php" class="brand" aria-label="<?= e(SITE['legal']) ?> — accueil">
      <?php include __DIR__ . '/logo.svg.php'; ?>
      <span class="brand__text">
        <span class="dsp brand__name">BAB'S-BAT <small>SARL</small></span>
        <span class="mono brand__tag"><?= e(SITE['slogan']) ?></span>
      </span>
    </a>
    <nav aria-label="Navigation principale" class="mono nav">
      <?php foreach (NAV as $file => $label): ?>
        <a href="<?= $file ?>"<?= $file === $page ? ' aria-current="page"' : '' ?>><?= e($label) ?></a>
      <?php endforeach; ?>
      <a href="contact.php" class="nav__cta">Devis gratuit →</a>
    </nav>
  </div>
</header>

<main id="contenu">
