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
        <img src="<?= e($img) ?>" alt="<?= e($label) ?>" loading="lazy">
        <figcaption><span class="mono"><?= e($cat) ?> · Songon</span><span><?= e($label) ?></span></figcaption>
      </figure>
    <?php endforeach; ?>
  </div>
</section>

<section>
  <div class="wrap testimonial">
    <span class="mono eyebrow">Parole de client</span>
    <blockquote>
      <p class="dsp">« [Témoignage client à compléter.] »</p>
      <footer class="mono">[Nom du client] — [Projet, quartier]</footer>
    </blockquote>
  </div>
</section>

<section class="t-orange">
  <div class="wrap cta-band__inner">
    <h2 class="dsp h-cta">Le prochain mur, c'est le vôtre</h2>
    <a href="contact.php" class="btn btn--dark">Démarrer mon projet →</a>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
