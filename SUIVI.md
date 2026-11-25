# Suivi du projet — site BAB'S-BAT SARL

Document de travail : ce qui reste à faire, les décisions en attente et l'historique.
Mettre à jour en cochant `[x]` au fur et à mesure. Dernière mise à jour : 25/11/2026.

- Site en ligne : https://babsbat-ci.kronyxtech.com (Dokploy, déploie la branche `main`)
- Branches : on travaille sur `dev`, puis on met à jour `main` une fois validé
- Documentation technique : [README.md](README.md)

---

## 1. Décision bloquante

- [ ] **Choisir le domaine définitif du site**
  - `babsbatsarl-ci.com` : à faire pointer vers le serveur Dokploy (enregistrement DNS A) puis l'ajouter dans Dokploy → Domains (HTTPS)
  - ou `babsbat-ci.kronyxtech.com` : on le garde comme adresse officielle
  - Problème actuel : les balises `canonical`, `og:url` et `og:image` pointent vers `https://babsbatsarl-ci.com`,
    qui ne répond pas. Google peut mal indexer le site et les aperçus de partage n'ont pas d'image.
  - Code concerné : `SITE['url']` dans `includes/config.php` (à rendre configurable par une variable `SITE_URL`)

---

## 2. SEO et partage (à faire)

- [ ] Variable d'environnement `SITE_URL` pour l'adresse officielle (canonical, og:url, og:image, sitemap)
- [ ] `robots.txt` (autoriser tout, indiquer le sitemap, bloquer `/contact?envoye=1`)
- [ ] `sitemap.xml` généré automatiquement (`/`, `/metiers`, `/chantiers`, `/contact`) avec dates de mise à jour
- [ ] Données structurées JSON-LD :
  - [ ] `LocalBusiness` / `GeneralContractor` : nom, logo, adresse (Riviera Faya, Cocody, Abidjan), BP, téléphones,
        e-mail, horaires, zone servie (Abidjan, Songon, Bingerville), coordonnées GPS
  - [ ] `VideoObject` pour le film « La terre devient maison » (miniature `img/film-poster.jpg`, durée PT45S)
  - [ ] `BreadcrumbList` sur les pages intérieures
- [ ] Cartes X/Twitter (`twitter:card` = `summary_large_image`)
- [ ] `og:image` : dimensions (`og:image:width/height`), texte alternatif, une image adaptée par page
- [ ] Icônes : `favicon.ico`, `apple-touch-icon.png` (180 px), icônes PNG 192/512 px, `site.webmanifest`
- [ ] Titres de page : remplacer le tiret cadratin « — » par « | » (ex. `Nos métiers | BAB'S-BAT SARL`)
- [ ] Relire les meta descriptions (150 à 160 caractères, mots-clés : BTCS, construction, villa, Abidjan, Songon)

### À faire par le client (hors code)
- [ ] Google Search Console : ajouter le domaine définitif, vérifier, soumettre `sitemap.xml`
- [ ] Google Business Profile : créer ou mettre à jour la fiche (adresse, horaires, photos, lien du site, téléphone)
- [ ] Bing Webmaster Tools (optionnel) : importer depuis Search Console

---

## 3. Performance

- [ ] Convertir les images en WebP (avec repli JPEG) : les rendus 3D pèsent 400 à 450 Ko chacun
      (`render-*.jpg`), les photos de chantier 150 à 230 Ko. Objectif : moins de 150 Ko par image
- [ ] Ajouter `width` / `height` sur toutes les images (éviter les décalages de mise en page, CLS)
- [ ] Mesurer avec Lighthouse / PageSpeed Insights (mobile) avant et après
- [ ] Envisager d'héberger les polices (Anton, Barlow) sur le site au lieu de Google Fonts (vitesse + RGPD)
- [ ] Film du héros : exporter en 1920×1080 depuis Claude Design et remplacer
      `La_terre_devient_maison.mp4` sur MinIO (même nom, aucun changement de code)
- [ ] Compresser `video-4.mp4` (12,6 Mo pour 73 s, cible 4 à 5 Mo)

---

## 4. Contenu à compléter

- [ ] Noms (et lieu / projet) des clients des témoignages, avec leur accord : `chantiers.php`, tableau `$testimonials`
- [ ] Vérifier les légendes des 4 vidéos (déduites des miniatures) : `includes/config.php`, `VIDEOS`
- [ ] Confirmer la résistance de la brique : 7 MPa (maquette) ou 60 bars = 6 MPa (ancien site)
- [ ] Conditions de location-vente (apport, durée) si on veut les afficher au lieu de « sur demande de devis »
- [ ] Prix et surfaces des lots si on veut les afficher
- [ ] Liens réseaux sociaux (Facebook, Instagram, LinkedIn, YouTube, TikTok) : aucun pour l'instant
- [ ] Numéros RCCM / NCC (mentions légales)
- [ ] Coordonnées GPS exactes du bureau (carte Google Maps et données structurées)

---

## 5. Pages et fonctionnalités à envisager

- [ ] Page **Mentions légales** + **Politique de confidentialité** (formulaire, reCAPTCHA, cookies Google) — recommandé
- [ ] Page « Qui sommes-nous » (l'ancienne v1 en avait une ; actuellement redirigée vers l'accueil `#apropos`)
- [ ] Statistiques de visite respectueuses de la vie privée (Plausible, Umami ou Matomo) ou Google Analytics 4
- [ ] Bouton WhatsApp flottant sur mobile
- [ ] Version 9:16 du film pour les réseaux sociaux (Reels, TikTok, Status WhatsApp)

---

## 6. Vérifications à faire après mise en ligne

- [ ] Rendu de l'e-mail de devis dans Gmail, Outlook et sur mobile (logo, couleurs, boutons)
- [ ] reCAPTCHA Enterprise : vérifier dans les logs Dokploy qu'il n'y a pas « reCAPTCHA Enterprise — erreur de configuration »
- [ ] Consulter régulièrement `storage/spam.csv` (faux positifs du filtre anti-spam)
- [ ] Sauvegarde du volume `storage/` (demandes de devis) : prévoir une sauvegarde régulière dans Dokploy

---

## 7. Fait (historique)

### Novembre 2026
- [x] Ancienne version archivée dans `old/` (non publiée)
- [x] Nouvelle version à partir de la maquette : PHP, CSS commun, en-tête / pied de page partagés
- [x] Pages : accueil, métiers, chantiers (galerie filtrable + visionneuse), contact, page 404
- [x] URL propres (`/metiers`, `/chantiers`, `/contact`) + redirections 301 des anciennes adresses (.php, .html, v1)
- [x] En-tête collant, menu mobile, bouton « haut de page », responsive vérifié de 320 à 1920 px
- [x] Contenus : chiffres clés, mot du gérant, 3 témoignages, légendes vidéos, délai de rappel 48 à 72 h
- [x] Vidéos servies depuis MinIO ; film « La terre devient maison » dans le héros
- [x] Formulaire de devis : validation serveur, CSRF, champ piège, délai minimal, limite par IP,
      filtre de contenu, reCAPTCHA Enterprise, indicateur de chargement
- [x] Envoi SMTP (OVH) avec e-mail HTML aux couleurs du site + sauvegarde `storage/demandes.csv`
- [x] Diagnostic SMTP : `php bin/smtp-test.php [--send]`
- [x] Déploiement Dokploy (Dockerfile php:8.3-apache), redirections en HTTPS derrière le proxy
- [x] Bases SEO : titre et description par page, Open Graph, `lang="fr"`, titres hiérarchisés, textes alternatifs
