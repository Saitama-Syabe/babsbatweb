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

  /* En-tête collant : hauteur (pour les ancres), ombre au défilement, menu mobile */
  var header = document.querySelector('[data-header]');
  if (header) {
    var root = document.documentElement;
    var setH = function () { root.style.setProperty('--header-h', header.offsetHeight + 'px'); };
    setH();
    window.addEventListener('resize', setH);
    window.addEventListener('load', setH);
    var shade = function () { header.classList.toggle('is-scrolled', window.scrollY > 4); };
    window.addEventListener('scroll', shade, { passive: true });
    shade();

    var toggle = header.querySelector('[data-nav-toggle]');
    var nav = document.getElementById('menu-principal');
    var label = header.querySelector('[data-nav-label]');
    if (toggle && nav) {
      var setOpen = function (open) {
        nav.classList.toggle('is-open', open);
        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        if (label) label.textContent = open ? 'Fermer' : 'Menu';
      };
      toggle.addEventListener('click', function () { setOpen(!nav.classList.contains('is-open')); });
      nav.addEventListener('click', function (e) { if (e.target.closest('a')) setOpen(false); });
      document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && nav.classList.contains('is-open')) { setOpen(false); toggle.focus(); }
      });
      document.addEventListener('click', function (e) {
        if (nav.classList.contains('is-open') && !header.contains(e.target)) setOpen(false);
      });
      window.matchMedia('(min-width: 761px)').addEventListener('change', function (m) { if (m.matches) setOpen(false); });
    }
  }

  /* Bouton « haut de page » */
  var toTop = document.querySelector('[data-to-top]');
  if (toTop) {
    var ticking = false;
    function onScroll() {
      toTop.classList.toggle('is-visible', window.scrollY > 600);
      ticking = false;
    }
    window.addEventListener('scroll', function () {
      if (!ticking) { ticking = true; requestAnimationFrame(onScroll); }
    }, { passive: true });
    onScroll();
    toTop.addEventListener('click', function (e) {
      e.preventDefault();
      var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
      window.scrollTo({ top: 0, behavior: reduce ? 'auto' : 'smooth' });
      // Replace le focus clavier en haut de page
      var brand = document.querySelector('.brand');
      if (brand) brand.focus({ preventScroll: true });
    });
  }

  /* Film de présentation (héros de l'accueil) */
  var preview = document.querySelector('[data-film-preview]');
  var filmDlg = document.querySelector('[data-film-dialog]');
  if (preview) {
    var conn = navigator.connection || {};
    var calm = window.matchMedia('(prefers-reduced-motion: reduce)').matches || conn.saveData === true;
    // Aperçu muet : lecture seulement quand le héros est visible (sinon pause, pour économiser batterie et données)
    if (!calm && 'IntersectionObserver' in window) {
      new IntersectionObserver(function (entries) {
        entries.forEach(function (en) {
          if (en.isIntersecting && !(filmDlg && filmDlg.open)) {
            var p = preview.play();
            if (p && p.catch) p.catch(function () {}); // lecture auto refusée : l'image d'aperçu reste
          } else {
            preview.pause();
          }
        });
      }, { threshold: 0.25 }).observe(preview);
    }
  }
  if (filmDlg && typeof filmDlg.showModal === 'function') {
    var full = filmDlg.querySelector('[data-film-full]');
    var opener = document.querySelector('[data-film-open]');
    var openFilm = function () {
      if (preview) preview.pause();
      filmDlg.showModal();
      document.body.classList.add('lb-open');
      full.currentTime = 0;
      var p = full.play();
      if (p && p.catch) p.catch(function () {});
    };
    if (opener) opener.addEventListener('click', openFilm);
    filmDlg.querySelector('[data-film-close]').addEventListener('click', function () { filmDlg.close(); });
    filmDlg.addEventListener('click', function (e) {
      if (e.target === filmDlg || e.target.classList.contains('film-dialog__stage')) filmDlg.close();
    });
    filmDlg.addEventListener('close', function () {
      full.pause();
      document.body.classList.remove('lb-open');
      if (opener) opener.focus();
      if (preview && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        var p = preview.play();
        if (p && p.catch) p.catch(function () {});
      }
    });
  } else if (document.querySelector('[data-film-open]')) {
    // Navigateur sans <dialog> : le bouton ouvre simplement la vidéo
    document.querySelector('[data-film-open]').addEventListener('click', function () {
      window.location.href = preview ? preview.currentSrc || preview.src : '#';
    });
  }

  /* Indicateur de chargement du formulaire de devis (bouton « Envoi en cours… », anti double-clic) */
  function setLoading(form, on) {
    var btn = form.querySelector('[type="submit"]');
    if (!btn) return;
    if (on) {
      if (!btn.hasAttribute('data-label')) btn.setAttribute('data-label', btn.innerHTML);
      btn.innerHTML = '<span class="spinner" aria-hidden="true"></span>Envoi en cours…';
      btn.disabled = true;
      btn.setAttribute('aria-busy', 'true');
      btn.classList.add('is-loading');
      var live = form.querySelector('[data-form-status]');
      if (live) live.textContent = 'Envoi de votre demande en cours, veuillez patienter.';
    } else if (btn.hasAttribute('data-label')) {
      btn.innerHTML = btn.getAttribute('data-label');
      btn.disabled = false;
      btn.removeAttribute('aria-busy');
      btn.classList.remove('is-loading');
    }
  }
  document.querySelectorAll('form.form').forEach(function (form) {
    // Formulaire sans reCAPTCHA : le chargement démarre à l'envoi normal
    if (!form.hasAttribute('data-recaptcha')) {
      form.addEventListener('submit', function (e) {
        if (form.querySelector('[aria-busy="true"]')) { e.preventDefault(); return; } // double clic
        setLoading(form, true);
      });
    }
  });
  // Retour arrière du navigateur (page restaurée depuis le cache) : on réactive le bouton
  window.addEventListener('pageshow', function (e) {
    if (e.persisted) document.querySelectorAll('form.form').forEach(function (f) { setLoading(f, false); });
  });

  /* reCAPTCHA v3 : obtient un jeton juste avant l'envoi du formulaire de devis */
  var rcForm = document.querySelector('form[data-recaptcha]');
  if (rcForm) {
    var rcKey = rcForm.getAttribute('data-recaptcha');
    var rcAction = rcForm.getAttribute('data-recaptcha-action') || 'devis';
    var rcField = rcForm.querySelector('input[name="g-recaptcha-response"]');

    var rcBusy = false;
    var sent = false;
    // Envoi unique : avec le jeton s'il est arrivé, sinon sans (le serveur affichera un message clair)
    function sendForm() {
      if (sent) return;
      sent = true;
      rcForm.submit();
    }
    rcForm.addEventListener('submit', function (e) {
      e.preventDefault();
      if (rcBusy) return;
      rcBusy = true;
      setLoading(rcForm, true);
      // reCAPTCHA Enterprise (grecaptcha.enterprise) ou classique (grecaptcha)
      var api = window.grecaptcha && (window.grecaptcha.enterprise || window.grecaptcha);
      if (!api || !api.execute) { sendForm(); return; } // script Google bloqué
      // Filet de sécurité : si Google ne répond pas (clé invalide, réseau), on envoie quand même au bout de 8 s
      var guard = setTimeout(sendForm, 8000);
      try {
        api.ready(function () {
          api.execute(rcKey, { action: rcAction }).then(function (token) {
            clearTimeout(guard);
            rcField.value = token;
            sendForm();
          }, function () { clearTimeout(guard); sendForm(); });
        });
      } catch (err) { clearTimeout(guard); sendForm(); }
    });
  }

  /* Focus sur le message de confirmation / d'erreur du formulaire */
  var focusEl = document.querySelector('[data-autofocus]');
  if (focusEl) focusEl.focus();
})();
