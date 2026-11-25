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

  /* Focus sur le message de confirmation / d'erreur du formulaire */
  var focusEl = document.querySelector('[data-autofocus]');
  if (focusEl) focusEl.focus();
})();
