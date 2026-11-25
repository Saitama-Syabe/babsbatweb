<?php
session_start();
require_once __DIR__ . '/includes/devis.php';

['errors' => $errors, 'old' => $old] = devis_handle();

$sent = isset($_GET['envoye']) && isset($_SESSION['devis_ok']);
$sentService = $sent ? $_SESSION['devis_ok'] : '';
if ($sent) {
    unset($_SESSION['devis_ok']);
}
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

/** Attributs d'erreur pour un champ */
function field_err(array $errors, string $name): string
{
    return isset($errors[$name]) ? ' aria-invalid="true" aria-describedby="err-' . $name . '"' : '';
}
function field_msg(array $errors, string $name): string
{
    return isset($errors[$name]) ? '<span class="field-error" id="err-' . $name . '">' . e($errors[$name]) . '</span>' : '';
}

$page        = 'contact.php';
$title       = "Contact et devis gratuit — BAB'S-BAT SARL";
$description = "Demandez un devis gratuit et sans engagement à BAB'S-BAT SARL : construction en BTCS, lotissement, location-vente, gestion immobilière. Riviera Faya, Abidjan.";
require __DIR__ . '/includes/header.php';
?>

<section class="t-brick">
  <div class="wrap page-hero__inner">
    <span class="mono">Contact · Devis gratuit et sans engagement</span>
    <h1 class="dsp h-hero">Posons la<br>première brique<span class="dot-ink">.</span></h1>
  </div>
</section>

<section>
  <div class="wrap contact-grid">

    <div class="contact-main" id="formulaire">
      <?php if ($sent): ?>
        <div class="thanks" role="status" tabindex="-1" data-autofocus>
          <span class="mono">Demande reçue</span>
          <h2 class="dsp">La première brique est posée</h2>
          <p>Merci ! Un conseiller BAB'S-BAT vous rappelle sous 48 à 72 h pour préciser votre projet (<?= e(mb_strtolower($sentService)) ?>).</p>
          <a href="contact" class="btn btn--outline-light">Envoyer une autre demande</a>
        </div>
      <?php else: ?>
        <?php if ($errors): ?>
          <div class="alert" role="alert" tabindex="-1" data-autofocus>
            <?= e($errors['global'] ?? 'Certains champs sont à corriger, voir ci-dessous.') ?>
          </div>
        <?php endif; ?>

        <form class="form" method="post" action="contact#formulaire" novalidate<?php if (RECAPTCHA_ON): ?> data-recaptcha="<?= e(RECAPTCHA['site_key']) ?>" data-recaptcha-action="<?= e(RECAPTCHA['action']) ?>"<?php endif; ?>>
          <input type="hidden" name="csrf" value="<?= e($_SESSION['csrf']) ?>">
          <?php if (RECAPTCHA_ON): ?><input type="hidden" name="g-recaptcha-response" value=""><?php endif; ?>
          <div class="hp" aria-hidden="true">
            <label for="site_web">Ne pas remplir</label>
            <input type="text" id="site_web" name="site_web" tabindex="-1" autocomplete="off">
          </div>

          <fieldset>
            <legend class="mono">01 — Choisissez votre brique</legend>
            <div class="chips">
              <?php foreach (SERVICES as $key => $label): ?>
                <label class="chip">
                  <input type="radio" name="service" value="<?= e($key) ?>"<?= $old['service'] === $key ? ' checked' : '' ?>>
                  <span><?= str_replace('Vidéosurveillance', 'Vidéo&shy;surveillance', e($label)) ?></span>
                </label>
              <?php endforeach; ?>
            </div>
            <?= field_msg($errors, 'service') ?>
          </fieldset>

          <fieldset>
            <legend class="mono">02 — Vos coordonnées</legend>
            <div class="fields">
              <div class="field">
                <label for="nom">Nom et prénom <span class="req" aria-hidden="true">*</span></label>
                <input id="nom" name="nom" type="text" autocomplete="name" placeholder="Ex. Kouassi Aya" required maxlength="100" value="<?= e($old['nom']) ?>"<?= field_err($errors, 'nom') ?>>
                <?= field_msg($errors, 'nom') ?>
              </div>
              <div class="field">
                <label for="tel">Téléphone <span class="req" aria-hidden="true">*</span></label>
                <input id="tel" name="tel" type="tel" autocomplete="tel" placeholder="+225 07 00 00 00 00" required maxlength="25" value="<?= e($old['tel']) ?>"<?= field_err($errors, 'tel') ?>>
                <?= field_msg($errors, 'tel') ?>
              </div>
              <div class="field">
                <label for="mail">E-mail</label>
                <input id="mail" name="mail" type="email" autocomplete="email" placeholder="vous@exemple.com" maxlength="150" value="<?= e($old['mail']) ?>"<?= field_err($errors, 'mail') ?>>
                <?= field_msg($errors, 'mail') ?>
              </div>
              <div class="field">
                <label for="lieu">Lieu du projet</label>
                <input id="lieu" name="lieu" type="text" placeholder="Commune, quartier" maxlength="150" value="<?= e($old['lieu']) ?>"<?= field_err($errors, 'lieu') ?>>
                <?= field_msg($errors, 'lieu') ?>
              </div>
            </div>
          </fieldset>

          <fieldset>
            <legend class="mono">03 — Votre projet</legend>
            <div class="stack gap-12">
              <div class="field">
                <label for="budget">Budget envisagé</label>
                <select id="budget" name="budget">
                  <?php foreach (BUDGETS as $b): ?>
                    <option<?= $old['budget'] === $b ? ' selected' : '' ?>><?= e($b) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="field">
                <label for="msg">Votre message</label>
                <textarea id="msg" name="msg" rows="5" maxlength="3000" placeholder="Type de bien, surface, délais…"<?= field_err($errors, 'msg') ?>><?= e($old['msg']) ?></textarea>
                <?= field_msg($errors, 'msg') ?>
              </div>
            </div>
          </fieldset>

          <div class="form__foot">
            <span class="form__note"><span class="req">*</span> Champs obligatoires. Vos données ne servent qu'à traiter votre demande.</span>
            <button type="submit" class="btn btn--orange form__submit">Envoyer ma demande →</button>
          </div>
          <?php if (RECAPTCHA_ON): ?>
          <p class="recaptcha-note">Ce formulaire est protégé par reCAPTCHA. Les
            <a href="https://policies.google.com/privacy" target="_blank" rel="noopener">règles de confidentialité</a> et les
            <a href="https://policies.google.com/terms" target="_blank" rel="noopener">conditions d'utilisation</a> de Google s'appliquent.</p>
          <?php endif; ?>
        </form>
      <?php endif; ?>
    </div>

    <aside class="contact-aside" aria-label="Coordonnées">
      <div class="card card--ink">
        <span class="mono card__label">Téléphone</span>
        <a href="tel:<?= e(PHONES['main'][1]) ?>" class="dsp card__phone">07 88 37 37 37</a>
        <a href="tel:<?= e(PHONES['mobile'][1]) ?>"><?= e(PHONES['mobile'][0]) ?></a>
        <a href="tel:<?= e(PHONES['fixe'][1]) ?>"><?= e(PHONES['fixe'][0]) ?> (fixe)</a>
      </div>
      <div class="quick">
        <a href="<?= e(WHATSAPP) ?>" class="quick__wa" target="_blank" rel="noopener">WhatsApp</a>
        <a href="mailto:<?= e(SITE['email']) ?>" class="quick__mail">E-mail</a>
      </div>
      <div class="card card--sand">
        <span class="mono card__label">Adresse</span>
        <span><?= e(SITE['address']) ?></span>
        <span><?= e(SITE['po_box']) ?></span>
        <a href="mailto:<?= e(SITE['email']) ?>"><?= e(SITE['email']) ?></a>
      </div>
      <div class="card card--sand">
        <span class="mono card__label">Horaires</span>
        <?php foreach (SITE['hours'] as $h): ?><span><?= e($h) ?></span><?php endforeach; ?>
      </div>
      <iframe class="map" title="Plan d'accès : <?= e(SITE['map_query']) ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade"
        src="https://maps.google.com/maps?q=<?= urlencode(SITE['map_query']) ?>&amp;z=15&amp;output=embed"></iframe>
    </aside>
  </div>
</section>

<?php if (RECAPTCHA_ON && !$sent): ?>
<script src="https://www.google.com/recaptcha/<?= RECAPTCHA_MODE === 'enterprise' ? 'enterprise' : 'api' ?>.js?render=<?= urlencode(RECAPTCHA['site_key']) ?>" async defer></script>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
