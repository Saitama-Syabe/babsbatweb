<?php
$page        = 'metiers.php';
$title       = "Nos métiers — BAB'S-BAT SARL";
$description = "Construction en BTCS, lotissement, location-vente, gestion immobilière, vidéosurveillance et contrôle d'accès, équipement solaire, prestations de services : un seul interlocuteur.";
require __DIR__ . '/includes/header.php';
?>

<section class="t-green">
  <div class="wrap page-hero__inner">
    <span class="mono">Nos métiers · 07 rangs</span>
    <h1 class="dsp h-hero">Sept briques<span class="dot-orange">.</span></h1>
    <p class="lead page-hero__lead">Construire, lotir, vendre, gérer, sécuriser, alimenter, rénover. Chaque métier s'emboîte dans le suivant, avec un seul interlocuteur.</p>
  </div>
</section>

<section>
  <div class="wrap stack" style="padding-top: 72px; padding-bottom: var(--section-y)">

    <article id="construction" class="trade">
      <span class="mono trade__num">01</span>
      <h2 class="dsp trade__title">Construction</h2>
      <div class="trade__body">
        <p>Villas, duplex, immeubles et locaux professionnels, bâtis en briques BTCS que nous fabriquons nous-mêmes. Étude, gros œuvre, second œuvre et finitions.</p>
        <span class="mono trade__tags">Plans · Permis · Suivi de chantier</span>
      </div>
      <img class="trade__visual" src="img/render-facade.jpg" alt="Rendu 3D de villas en BTCS" loading="lazy">
    </article>

    <article id="lotissement" class="trade">
      <span class="mono trade__num">02</span>
      <h2 class="dsp trade__title">Lotissement</h2>
      <div class="trade__body">
        <p>Des terrains viabilisés dans des zones en plein essor, avec un dossier foncier vérifié pour acheter en toute sérénité.</p>
        <span class="mono trade__tags">Lots de [XXX] m² · À partir de [PRIX] FCFA</span>
      </div>
      <div class="trade__visual plots" aria-hidden="true"><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span></div>
    </article>

    <article id="location" class="trade">
      <span class="mono trade__num">03</span>
      <h2 class="dsp trade__title">Location-vente</h2>
      <div class="trade__body">
        <p>Habitez dès maintenant et devenez propriétaire au fil de vos mensualités. Un contrat clair, un échéancier adapté à vos revenus.</p>
        <span class="mono trade__tags">Apport [X] % · Jusqu'à [XX] ans</span>
      </div>
      <img class="trade__visual" src="img/render-garages.jpg" alt="Villas avec garages doubles" loading="lazy">
    </article>

    <article id="gestion" class="trade">
      <span class="mono trade__num">04</span>
      <h2 class="dsp trade__title">Gestion immobilière</h2>
      <div class="trade__body">
        <p>Vous vivez loin ou manquez de temps ? Nous gérons vos biens comme les nôtres, y compris pour la diaspora.</p>
        <span class="mono trade__tags">Locataires · Loyers · Entretien</span>
      </div>
      <div class="trade__visual trade__word" style="background: var(--sand)" aria-hidden="true"><span class="dsp" style="color: var(--clay)">Clés</span></div>
    </article>

    <article id="securite" class="trade">
      <span class="mono trade__num">05</span>
      <h2 class="dsp trade__title">Vidéosurveillance &amp; contrôle d'accès</h2>
      <div class="trade__body">
        <p>Pour les entreprises et les domiciles : étude du site, installation, consultation à distance et maintenance. Intégrée d'origine dans nos villas.</p>
        <span class="mono trade__tags">Caméras · Badges · Domotique</span>
      </div>
      <div class="trade__visual trade__word" style="background: var(--ink)" aria-hidden="true"><span class="dsp" style="color: var(--orange)">24 / 7</span></div>
    </article>

    <article id="solaire" class="trade">
      <span class="mono trade__num">06</span>
      <h2 class="dsp trade__title">Équipement solaire</h2>
      <div class="trade__body">
        <p>Vente et installation de panneaux, onduleurs et batteries pour alimenter votre maison ou vos locaux à l'énergie solaire.</p>
        <span class="mono trade__tags">Étude · Fourniture · Pose</span>
      </div>
      <div class="trade__visual panels" aria-hidden="true"><?= str_repeat('<span></span>', 12) ?></div>
    </article>

    <article id="prestations" class="trade">
      <span class="mono trade__num">07</span>
      <h2 class="dsp trade__title">Prestations de services</h2>
      <div class="trade__body">
        <p>Rénovation, extension, études et expertise : nos équipes interviennent aussi sur des travaux ponctuels.</p>
        <span class="mono trade__tags">Rénovation · Études · Sur mesure</span>
      </div>
      <img class="trade__visual" src="img/equipe-terrain.jpg" alt="Équipe BAB'S-BAT sur un chantier" loading="lazy">
    </article>
  </div>
</section>

<section class="t-orange">
  <div class="wrap cta-band__inner">
    <h2 class="dsp h-cta">Posons la première brique</h2>
    <a href="contact.php" class="btn btn--dark">Demander un devis gratuit →</a>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
