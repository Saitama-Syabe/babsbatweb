<?php
$page        = 'chantiers.php';
$title       = "Nos chantiers — BAB'S-BAT SARL";
$description = "Plans 3D, fabrication des briques BTCS et construction des villas de la cité de Songon, Institut Pasteur : suivez nos chantiers brique après brique.";
require __DIR__ . '/includes/header.php';

$projects = [
    ['Plans 3D',    'Villas 6 pièces, façade carports',      'img/render-carport.jpg', 3],
    ['Chantier',    'Gros œuvre et portails posés',           'img/chantier-3.jpg',     2],
    ['Fabrication', 'Briques BTCS en cure',                   'img/briques.jpg',        2],
    ['Chantier',    'Élévation des murs en BTCS',             'img/chantier-1.jpg',     2],
    ['Plans 3D',    'Villas avec garages doubles',            'img/render-garages.jpg', 3],
    ['Fabrication', 'Tamisage de la latérite',                'img/equipe-terrain.jpg', 2],
    ['Plans 3D',    'Façade arrière, escaliers hélicoïdaux',  'img/render-arriere.jpg', 2],
    ['Chantier',    'Terrasses et garde-corps',               'img/chantier-2.jpg',     3],
    ['Plans 3D',    'Terrasses végétalisées',                 'img/render-angle.jpg',   2],
];
$cats  = ['Tout', 'Chantier', 'Plans 3D', 'Fabrication'];
$count = count($projects);
?>

<section class="t-ink">
  <div class="wrap page-hero__inner" style="padding-bottom: 56px">
    <span class="mono">Chantiers · Cité de Songon, Institut Pasteur</span>
    <h1 class="dsp h-hero">Brique<br>après brique<span class="dot-orange">.</span></h1>
    <div class="row" style="gap: 20px 48px; align-items: flex-end; justify-content: space-between">
      <div class="filters" role="group" aria-label="Filtrer les photos" data-filters hidden>
        <?php foreach ($cats as $c): ?>
          <button type="button" class="filter-btn" data-filter="<?= e($c) ?>" aria-pressed="<?= $c === 'Tout' ? 'true' : 'false' ?>"><?= e($c) ?></button>
        <?php endforeach; ?>
      </div>
      <span class="mono" data-count aria-live="polite"><?= sprintf('%02d', $count) ?> photos</span>
    </div>
  </div>
</section>

<section class="t-ink">
  <div class="wrap gallery" data-gallery>
    <?php foreach ($projects as [$cat, $label, $img, $grow]): ?>
      <figure data-cat="<?= e($cat) ?>" style="--grow: <?= $grow ?>">
        <a href="<?= e($img) ?>" class="gallery__link" data-lightbox data-caption="<?= e($cat) ?> · Songon — <?= e($label) ?>" aria-label="Agrandir la photo : <?= e($label) ?>">
          <img src="<?= e($img) ?>" alt="<?= e($label) ?>" loading="lazy">
        </a>
        <figcaption><span class="mono"><?= e($cat) ?> · Songon</span><span><?= e($label) ?></span></figcaption>
      </figure>
    <?php endforeach; ?>
  </div>
</section>

<!-- Visionneuse photo -->
<dialog class="lightbox" data-lightbox-dialog aria-label="Visionneuse de photos">
  <div class="lightbox__bar">
    <span class="mono" data-lb-count></span>
    <button type="button" class="lightbox__btn lightbox__close" data-lb-close aria-label="Fermer">✕</button>
  </div>
  <div class="lightbox__stage">
    <button type="button" class="lightbox__btn lightbox__nav lightbox__nav--prev" data-lb-prev aria-label="Photo précédente">←</button>
    <figure class="lightbox__figure">
      <img data-lb-img alt="">
      <figcaption class="lightbox__caption" data-lb-caption></figcaption>
    </figure>
    <button type="button" class="lightbox__btn lightbox__nav lightbox__nav--next" data-lb-next aria-label="Photo suivante">→</button>
  </div>
</dialog>

<?php
// Témoignages validés par les clients. La section s'affiche s'il y en a au moins un.
// 'author' peut être remplacé par le nom du client (ex. 'M. Kouadio — Villa 6 pièces, Songon').
$testimonials = [
    ["Ce qui m'a convaincu, c'est la fraîcheur de la maison. Même en pleine saison chaude, on n'a presque pas besoin de climatiser.", "Propriétaire d'une villa en BTCS"],
    ["J'étais à l'étranger pendant toute la construction. Photos, vidéos, appels : j'ai suivi chaque étape comme si j'étais sur place.", 'Client de la diaspora'],
    ["Délais tenus, budget respecté, et une équipe toujours joignable. On a reçu nos clés sans mauvaise surprise.", "Client BAB'S-BAT"],
];
if ($testimonials): ?>
<section>
  <div class="wrap section stack gap-48">
    <div class="section-head__title">
      <span class="mono eyebrow">Parole de clients</span>
      <h2 class="dsp h-section">Ils ont posé<br>la première brique</h2>
    </div>
    <div class="testimonials">
      <?php foreach ($testimonials as $i => [$quote, $author]): ?>
        <blockquote class="testimonial-card<?= $i === 1 ? ' testimonial-card--brick' : '' ?>">
          <span class="dsp testimonial-card__mark" aria-hidden="true">«</span>
          <p><?= e($quote) ?></p>
          <footer class="mono"><?= e($author) ?></footer>
        </blockquote>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<section class="t-orange">
  <div class="wrap cta-band__inner">
    <h2 class="dsp h-cta">Le prochain mur, c'est le vôtre</h2>
    <a href="contact" class="btn btn--dark">Démarrer mon projet →</a>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
