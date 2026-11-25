<?php
$page        = 'index.php';
$title       = "BAB'S-BAT SARL — Maisons en briques de terre compressée à Abidjan";
$description = "BAB'S-BAT SARL fabrique ses briques BTCS et construit des villas fraîches, silencieuses et durables en Côte d'Ivoire. Construction, lotissement, location-vente, gestion immobilière.";
$footer      = 'full';
require __DIR__ . '/includes/header.php';
?>

<!-- Héros : la photo en mur de briques -->
<section class="t-brick">
  <div class="wrap hero__inner">
    <div class="hero__meta">
      <span class="mono">BTP · Abidjan, Côte d'Ivoire</span>
      <span class="mono">Briques de terre compressée et stabilisée</span>
    </div>
    <h1 class="dsp h-hero">La terre<br>devient maison<span class="dot-ink">.</span></h1>
    <div class="hero__body">
      <div class="hero__text">
        <p class="lead">Nous fabriquons nos briques à partir de la latérite, puis nous bâtissons des maisons fraîches, silencieuses et durables. Du terrain à la remise des clés.</p>
        <div class="hero__actions">
          <a href="contact.php" class="btn btn--orange">Demander un devis gratuit</a>
          <a href="#matiere" class="btn btn--outline-light">Découvrir la BTCS ↓</a>
        </div>
      </div>
      <div class="wall">
        <img src="img/render-carport.jpg" alt="Villas BAB'S-BAT en BTCS à Songon, vues à travers un appareillage de briques" fetchpriority="high">
        <div class="wall__courses" aria-hidden="true">
          <div class="course"></div><div class="course off"></div><div class="course"></div><div class="course off"></div><div class="course"></div><div class="course off"></div>
        </div>
        <div class="wb" style="left: 62.5%; top: 16.667%; background-color: var(--orange); color: var(--ink)">
          <span class="dsp" style="font-size: 3.1cqw; white-space: nowrap">75 M FCFA</span>
          <span class="mono" style="font-size: 1.5cqw">Villa clé en main</span>
        </div>
        <div class="wb" style="left: 0; top: 66.667%; background-color: var(--green); color: #fff">
          <span class="dsp" style="font-size: 4.6cqw">BTCS</span>
        </div>
        <div class="wb" style="left: 37.5%; top: 83.333%; background-color: var(--paper); color: var(--ink)">
          <span class="dsp" style="font-size: 3.6cqw">Songon</span>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Bandeau métiers -->
<div class="t-ink">
  <div class="wrap dsp ticker">
    <span>Construction</span><i aria-hidden="true"></i>
    <span>Lotissement</span><i aria-hidden="true"></i>
    <span>Location-vente</span><i aria-hidden="true"></i>
    <span>Gestion immobilière</span><i aria-hidden="true"></i>
    <span>Vidéosurveillance</span><i aria-hidden="true"></i>
    <span>Solaire</span><i aria-hidden="true"></i>
    <span>Prestations</span>
  </div>
</div>

<!-- Rang 01 : la matière -->
<section id="matiere">
  <div class="wrap section stack gap-56">
    <div class="section-head">
      <div class="section-head__title" style="flex: 1 1 560px">
        <span class="mono eyebrow">Rang 01 — La matière</span>
        <h2 class="dsp h-section">Une brique née<br>du sol ivoirien</h2>
      </div>
      <p class="text-lg" style="flex: 1 1 380px">La brique de terre compressée et stabilisée (BTCS) est pleine et autobloquante, fabriquée sans cuisson. Notre procédé vient de la technologie Hydraform, développée en Afrique du Sud et éprouvée depuis plus de 100 ans. Elle réduit les émissions de CO₂ et la consommation de fer, de ciment, de gravier et de peinture.</p>
    </div>

    <!-- La recette -->
    <div class="stack gap-12">
      <span class="mono">La recette d'une brique</span>
      <div class="recipe" role="img" aria-label="Composition : 80 % de latérite tamisée, 10 % de sable blanc, 10 % de ciment">
        <div class="recipe__laterite"><span class="dsp" style="font-size: 56px">80 %</span><span style="font-weight: 600; font-size: 17px">latérite tamisée</span></div>
        <div class="recipe__sable"><span class="dsp" style="font-size: 26px">10 %</span></div>
        <div class="recipe__ciment"><span class="dsp" style="font-size: 26px">10 %</span></div>
      </div>
      <div class="mono recipe__legend">
        <span><span class="swatch" style="background: #E3D3B4" aria-hidden="true"></span>Sable blanc tamisé</span>
        <span><span class="swatch" style="background: #8B8781" aria-hidden="true"></span>Ciment</span>
        <span>→ compressé à plus de 7 MPa</span>
      </div>
    </div>

    <div class="row" style="gap: 48px 64px; align-items: flex-start">
      <!-- Fiche technique -->
      <div class="spec">
        <div class="mono" style="display: flex; justify-content: space-between; gap: 16px"><span>Fiche technique</span><span>3 formats</span></div>
        <div class="spec__formats">
          <?php foreach ([[220, '#B5451B', '#983817'], [180, '#C77B4E', '#A8623A'], [150, '#D9A07A', '#BD8258']] as [$mm, $b, $g]): ?>
          <div class="spec__format" style="flex: <?= $mm ?> 1 0">
            <div class="spec__brick" style="--b: <?= $b ?>; --g: <?= $g ?>" aria-hidden="true"><span></span><span></span></div>
            <div class="spec__dim" aria-hidden="true"></div>
            <span class="mono mono--plain" style="text-align: center">Brique de <?= $mm ?> mm</span>
          </div>
          <?php endforeach; ?>
        </div>
        <img src="img/briques.jpg" alt="Briques BTCS en cure sur notre site de production" loading="lazy">
      </div>
      <!-- Atouts -->
      <ol class="numlist">
        <?php
        $atouts = [
            'Isolation thermique : une fraîcheur constante dans la maison',
            'Isolation phonique',
            'Résiste aux incendies',
            "À l'épreuve des balles",
            "Ne conduit pas l'humidité",
            'Écologique, durable et très esthétique',
        ];
        foreach ($atouts as $i => $txt): ?>
          <li><span class="mono"><?= sprintf('%02d', $i + 1) ?></span><span class="numlist__text"><?= e($txt) ?></span></li>
        <?php endforeach; ?>
      </ol>
    </div>
  </div>
</section>

<!-- Rang 02 : du plan au chantier -->
<section class="t-ink">
  <div class="wrap section stack gap-48">
    <div class="section-head__title">
      <span class="mono eyebrow sw-orange">Rang 02 — Programme en cours · Songon, Institut Pasteur</span>
      <h2 class="dsp h-section">Du plan<br>au chantier</h2>
    </div>
    <div class="row" style="gap: 40px 56px; align-items: flex-start">
      <div class="stack" style="flex: 3 1 560px; min-width: 0; gap: 16px">
        <div class="compare" data-compare>
          <img src="img/chantier-2.jpg" alt="Les villas de Songon en cours de construction">
          <div class="compare__clip" data-compare-clip>
            <img src="img/render-garages.jpg" alt="Rendu 3D du projet de villas">
          </div>
          <div class="compare__line" data-compare-line aria-hidden="true"></div>
          <span class="mono compare__tag compare__tag--left">Le plan</span>
          <span class="mono compare__tag compare__tag--right">Le chantier</span>
        </div>
        <label for="cmp" class="mono">Glissez pour comparer le plan 3D et le chantier</label>
        <input id="cmp" class="compare-range" type="range" min="0" max="100" value="50" data-compare-range>
      </div>
      <div class="stack" style="flex: 2 1 360px; min-width: 0; gap: 28px">
        <div class="stack" style="gap: 6px">
          <span class="mono" style="color: var(--orange-light)">Livrée finie, clé en main</span>
          <strong class="dsp price__amount">75 millions</strong>
          <span class="mono">FCFA par villa</span>
        </div>
        <dl class="specs">
          <div><dt class="mono">Type</dt><dd>Villa 6 pièces</dd></div>
          <div><dt class="mono">Chambres</dt><dd>4 chambres autonomes</dd></div>
          <div><dt class="mono">Séjour</dt><dd>1 salon + 1 bureau</dd></div>
          <div><dt class="mono">Murs</dt><dd>BTCS, isolation thermique et phonique</dd></div>
          <div><dt class="mono">Connectée</dt><dd>Vidéosurveillance et domotique intégrées</dd></div>
        </dl>
        <a href="contact.php?service=villa-songon" class="btn btn--orange" style="align-self: flex-start">Réserver une villa →</a>
      </div>
    </div>
  </div>
</section>

<!-- Rang 03 : les métiers en mur de briques -->
<section>
  <div class="wrap section stack gap-48">
    <div class="section-head">
      <div class="section-head__title">
        <span class="mono eyebrow sw-green">Rang 03 — Nos métiers</span>
        <h2 class="dsp h-section">Sept briques,<br>un seul mur</h2>
      </div>
      <a href="metiers.php" class="mono" style="padding: 14px 0">Voir le détail des métiers →</a>
    </div>
    <div class="bricks">
      <div class="bricks__row">
        <a href="metiers.php#construction" class="brick-card brick-card--brick"><span class="mono">01</span><span class="brick-card__body"><span class="dsp brick-card__title">Construction</span><span class="brick-card__desc">Villas, duplex, immeubles et locaux en BTCS.</span></span></a>
        <a href="metiers.php#lotissement" class="brick-card"><span class="mono">02</span><span class="brick-card__body"><span class="dsp brick-card__title">Lotissement</span><span class="brick-card__desc">Terrains viabilisés, dossier foncier vérifié.</span></span></a>
        <a href="metiers.php#location" class="brick-card brick-card--clay"><span class="mono">03</span><span class="brick-card__body"><span class="dsp brick-card__title">Location-vente</span><span class="brick-card__desc">Devenez propriétaire au fil de vos mensualités.</span></span></a>
      </div>
      <div class="bricks__row">
        <div class="brick-half" aria-hidden="true" style="background: var(--green)"></div>
        <a href="metiers.php#gestion" class="brick-card"><span class="mono">04</span><span class="brick-card__body"><span class="dsp brick-card__title">Gestion immobilière</span><span class="brick-card__desc">Locataires, loyers, entretien : on gère pour vous.</span></span></a>
        <a href="metiers.php#securite" class="brick-card brick-card--ink"><span class="mono">05</span><span class="brick-card__body"><span class="dsp brick-card__title">Vidéosurveillance &amp; accès</span><span class="brick-card__desc">Caméras, badges et domotique, entreprises et domiciles.</span></span></a>
        <div class="brick-half" aria-hidden="true" style="background: var(--brick)"></div>
      </div>
      <div class="bricks__row">
        <a href="metiers.php#solaire" class="brick-card brick-card--clay"><span class="mono">06</span><span class="brick-card__body"><span class="dsp brick-card__title">Équipement solaire</span><span class="brick-card__desc">Vente et installation de panneaux et kits.</span></span></a>
        <a href="metiers.php#prestations" class="brick-card"><span class="mono">07</span><span class="brick-card__body"><span class="dsp brick-card__title">Prestations de services</span><span class="brick-card__desc">Rénovation, études et travaux sur mesure.</span></span></a>
        <a href="contact.php" class="brick-card brick-card--orange"><span class="mono">Votre projet</span><span class="dsp brick-card__title">Posons la première brique →</span></a>
      </div>
    </div>
  </div>
</section>

<!-- Rang 04 : vidéos -->
<section id="videos" class="t-brick">
  <div class="wrap section stack gap-48">
    <div class="section-head__title">
      <span class="mono eyebrow sw-ink">Rang 04 — Sur le terrain</span>
      <h2 class="dsp h-section">Le chantier,<br>sans filtre</h2>
    </div>
    <div class="videos">
      <?php
      // TODO légendes à fournir par le client
      $videos = [
          ['video/video-1.mp4', '[Légende vidéo 1]', 'cover'],
          ['video/video-2.mp4', '[Légende vidéo 2]', 'contain'],
          ['video/video-3.mp4', '[Légende vidéo 3]', 'cover'],
          ['video/video-4.mp4', '[Légende vidéo 4]', 'cover'],
      ];
      foreach ($videos as $i => [$src, $caption, $fit]): $n = sprintf('%02d', $i + 1); ?>
      <figure>
        <video src="<?= $src ?>" controls muted playsinline preload="metadata" aria-label="Vidéo de chantier <?= $i + 1 ?>" style="object-fit: <?= $fit ?>"></video>
        <figcaption class="mono"><?= $n ?> — <?= e($caption) ?></figcaption>
      </figure>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Rang 05 : le parcours -->
<section>
  <div class="wrap section stack gap-48">
    <div class="section-head__title">
      <span class="mono eyebrow sw-clay">Rang 05 — Votre parcours</span>
      <h2 class="dsp h-section">Le mur monte,<br>étape par étape</h2>
    </div>
    <ol class="steps">
      <li><span class="mono">Étape 01</span><strong class="dsp">Le terrain</strong><span>Visite et vérification des documents.</span></li>
      <li><span class="mono">Étape 02</span><strong class="dsp">Plans &amp; devis</strong><span>Conception et chiffrage détaillé.</span></li>
      <li><span class="mono">Étape 03</span><strong class="dsp">Construction</strong><span>Briques fabriquées et posées par nos équipes.</span></li>
      <li><span class="mono">Étape 04</span><strong class="dsp">Remise des clés</strong><span>Villa livrée finie, prête à habiter.</span></li>
    </ol>
  </div>
</section>

<!-- Rang 06 : l'entreprise -->
<section id="apropos" class="t-sand">
  <div class="wrap section about">
    <div class="about__photos">
      <img src="img/equipe-terrain.jpg" alt="L'équipe prépare la latérite sur un chantier" loading="lazy">
      <img src="img/gilet.jpg" alt="Gilet de chantier aux couleurs de BAB'S-BAT" loading="lazy">
    </div>
    <div class="about__text">
      <span class="mono eyebrow sw-green">Rang 06 — L'entreprise</span>
      <h2 class="dsp about__title">Des bâtisseurs ivoiriens, de la terre aux clés</h2>
      <p class="text-lg">BAB'S-BAT SARL est une entreprise de BTP spécialisée dans la construction de bâtiments en briques de terre comprimée et stabilisée. Nous fabriquons, bâtissons, lotissons, gérons et sécurisons les biens de nos clients.</p>
      <blockquote class="quote">
        <p>« La BTCS consomme moins de ciment, moins de fer, et garde la fraîcheur sans climatisation. Construire mieux, c'est aussi construire plus sobre. »</p>
        <footer class="mono"><?= e(SITE['manager']) ?> — Gérant</footer>
      </blockquote>
      <div class="stats">
        <div><strong class="dsp">[XX]</strong><span class="mono">ans d'expérience</span></div>
        <div><strong class="dsp">[XX]</strong><span class="mono">maisons livrées</span></div>
        <div><strong class="dsp">[XX]</strong><span class="mono">briques produites</span></div>
      </div>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
