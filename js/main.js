/* BAB'S-BAT — scripts communs (aucune dépendance) */
(function () {
  'use strict';

  /* Comparateur plan 3D / chantier (accueil) */
  document.querySelectorAll('[data-compare]').forEach(function (box) {
    var section = box.parentElement;
    var range = section.querySelector('[data-compare-range]');
    var clip = box.querySelector('[data-compare-clip]');
    var line = box.querySelector('[data-compare-line]');
    if (!range || !clip || !line) return;
    function update() {
      clip.style.clipPath = 'inset(0 ' + (100 - range.value) + '% 0 0)';
      line.style.left = 'calc(' + range.value + '% - 2px)';
    }
    range.addEventListener('input', update);
    update();
  });

  /* Filtres de la galerie (chantiers) */
  var filters = document.querySelector('[data-filters]');
  var gallery = document.querySelector('[data-gallery]');
  if (filters && gallery) {
    var buttons = filters.querySelectorAll('[data-filter]');
    var items = gallery.querySelectorAll('figure[data-cat]');
    var counter = document.querySelector('[data-count]');
    filters.hidden = false; // affichés seulement si JS actif
    filters.addEventListener('click', function (e) {
      var btn = e.target.closest('[data-filter]');
      if (!btn) return;
      var cat = btn.getAttribute('data-filter');
      buttons.forEach(function (b) { b.setAttribute('aria-pressed', b === btn ? 'true' : 'false'); });
      var n = 0;
      items.forEach(function (fig) {
        var show = cat === 'Tout' || fig.getAttribute('data-cat') === cat;
        fig.hidden = !show;
        if (show) n++;
      });
      if (counter) counter.textContent = (n < 10 ? '0' : '') + n + (n > 1 ? ' photos' : ' photo');
    });
  }

  /* Visionneuse photo (chantiers) */
  var dlg = document.querySelector('[data-lightbox-dialog]');
  if (dlg && typeof dlg.showModal === 'function') {
    var lbImg = dlg.querySelector('[data-lb-img]');
    var lbCap = dlg.querySelector('[data-lb-caption]');
    var lbCount = dlg.querySelector('[data-lb-count]');
    var lbPrev = dlg.querySelector('[data-lb-prev]');
    var lbNext = dlg.querySelector('[data-lb-next]');
    var list = [], idx = 0, opener = null;

    // Seules les photos visibles (filtre actif) font partie du diaporama
    function visibleLinks() {
      return Array.prototype.filter.call(document.querySelectorAll('[data-lightbox]'), function (a) {
        return !a.closest('figure').hidden;
      });
    }
    function show(i) {
      idx = (i + list.length) % list.length;
      var a = list[idx];
      lbImg.classList.add('is-loading');
      lbImg.onload = function () { lbImg.classList.remove('is-loading'); };
      lbImg.src = a.getAttribute('href');
      lbImg.alt = a.querySelector('img').alt;
      lbCap.textContent = a.getAttribute('data-caption') || '';
      lbCount.textContent = (idx + 1) + ' / ' + list.length;
      lbPrev.hidden = lbNext.hidden = list.length < 2;
      // Précharge les voisines
      [idx - 1, idx + 1].forEach(function (j) {
        var n = list[(j + list.length) % list.length];
        if (n) new Image().src = n.getAttribute('href');
      });
    }
    function open(a) {
      list = visibleLinks();
      opener = a;
      show(list.indexOf(a));
      dlg.showModal();
      document.body.classList.add('lb-open');
    }
    document.addEventListener('click', function (e) {
      var a = e.target.closest('[data-lightbox]');
      if (!a || e.ctrlKey || e.metaKey || e.shiftKey) return;
      e.preventDefault();
      open(a);
    });
    lbPrev.addEventListener('click', function () { show(idx - 1); });
    lbNext.addEventListener('click', function () { show(idx + 1); });
    dlg.querySelector('[data-lb-close]').addEventListener('click', function () { dlg.close(); });
    // Clic sur le fond = fermer
    dlg.addEventListener('click', function (e) {
      if (e.target === dlg || e.target.classList.contains('lightbox__stage') || e.target.classList.contains('lightbox__figure')) dlg.close();
    });
    dlg.addEventListener('keydown', function (e) {
      if (e.key === 'ArrowLeft') { e.preventDefault(); show(idx - 1); }
      else if (e.key === 'ArrowRight') { e.preventDefault(); show(idx + 1); }
    });
    dlg.addEventListener('close', function () {
      document.body.classList.remove('lb-open');
      lbImg.removeAttribute('src');
      if (opener) opener.focus();
    });
    // Glisser sur mobile
    var x0 = null;
    lbImg.addEventListener('touchstart', function (e) { x0 = e.touches[0].clientX; }, { passive: true });
    lbImg.addEventListener('touchend', function (e) {
      if (x0 === null) return;
      var dx = e.changedTouches[0].clientX - x0;
      if (Math.abs(dx) > 50) show(idx + (dx < 0 ? 1 : -1));
      x0 = null;
    });
  }

  /* Focus sur le message de confirmation / d'erreur du formulaire */
  var focusEl = document.querySelector('[data-autofocus]');
  if (focusEl) focusEl.focus();
})();
