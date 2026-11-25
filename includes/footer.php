<?php
/**
 * Pied de page commun.
 *   $footer = 'full'  (accueil : grand pied de page avec coordonnées)
 *   $footer = 'small' (autres pages)
 */
$footer = $footer ?? 'small';
$year   = date('Y');
?>
</main>

<footer class="site-footer">
<?php if ($footer === 'full'): ?>
  <div class="wrap footer-big">
    <div class="row" style="gap: 24px 48px; align-items: flex-end; justify-content: space-between">
      <p class="dsp footer-big__claim">Bâtissons<br>ensemble.</p>
      <div class="row" style="gap: 10px; padding-bottom: 12px">
        <a href="contact.php" class="btn btn--orange">Demander un devis</a>
        <a href="<?= e(WHATSAPP) ?>" class="btn btn--outline-light" target="_blank" rel="noopener">WhatsApp</a>
      </div>
    </div>
    <div class="footer-cols">
      <div style="flex: 2 1 300px">
        <span class="mono footer-label">Adresse</span>
        <span><?= e(SITE['address']) ?></span>
        <span><?= e(SITE['po_box']) ?></span>
      </div>
      <div style="flex: 1 1 220px">
        <span class="mono footer-label">Téléphone</span>
        <?php foreach (PHONES as [$label, $tel]): ?>
          <a href="tel:<?= e($tel) ?>"><?= e($label) ?></a>
        <?php endforeach; ?>
      </div>
      <div style="flex: 1 1 220px">
        <span class="mono footer-label">En ligne</span>
        <a href="mailto:<?= e(SITE['email']) ?>" class="accent"><?= e(SITE['email']) ?></a>
        <span><?= e(SITE['domain']) ?></span>
      </div>
    </div>
    <span class="mono footer-copy">© <?= $year ?> <?= e(SITE['legal']) ?> — <?= e(SITE['slogan']) ?></span>
  </div>
<?php else: ?>
  <div class="wrap footer-small">
    <span class="dsp footer-small__claim">Bâtissons ensemble.</span>
    <span class="mono mono--plain">
      <a href="tel:<?= e(PHONES['main'][1]) ?>"><?= e(PHONES['main'][0]) ?></a> ·
      <a href="mailto:<?= e(SITE['email']) ?>" class="accent"><?= e(SITE['email']) ?></a>
    </span>
    <span class="mono footer-copy">© <?= $year ?> <?= e(SITE['legal']) ?></span>
  </div>
<?php endif; ?>
</footer>

<a href="#" class="to-top" data-to-top aria-label="Remonter en haut de la page" title="Haut de page">
  <svg viewBox="0 0 24 24" width="24" height="24" aria-hidden="true" focusable="false"><path d="M12 5l-7 7m7-7l7 7M12 5v14" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="square"/></svg>
</a>

<script src="js/main.js?v=4" defer></script>
</body>
</html>
