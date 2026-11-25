<?php
http_response_code(404);
$page        = '404.php';
$title       = "Page introuvable — BAB'S-BAT SARL";
$description = "Cette page n'existe pas ou a été déplacée.";
$noindex     = true;
require __DIR__ . '/includes/header.php';
?>

<section class="t-brick">
  <div class="wrap page-hero__inner" style="padding-bottom: 72px">
    <span class="mono">Erreur 404 · Page introuvable</span>
    <h1 class="dsp h-hero">Brique<br>manquante<span class="dot-ink">.</span></h1>
    <p class="lead page-hero__lead">La page que vous cherchez n'existe pas ou a été déplacée. Reprenons les fondations.</p>
    <div class="hero__actions">
      <a href="./" class="btn btn--orange">Retour à l'accueil</a>
      <a href="contact" class="btn btn--outline-light">Demander un devis</a>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
