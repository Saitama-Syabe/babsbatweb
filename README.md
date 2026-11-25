# babsbatweb — site vitrine BAB'S-BAT SARL

Site PHP sans framework ni build (PHP 8+, Apache).

- Local (XAMPP) : http://localhost/personnalProject/babsbatweb/
- Ou : `php -S 127.0.0.1:8000 router.php` à la racine, puis http://127.0.0.1:8000
- Production : Docker / Dokploy (voir plus bas)

## Structure

| Chemin | Rôle |
|---|---|
| `index.php`, `metiers.php`, `chantiers.php`, `contact.php`, `404.php` | Les pages, servies en URL propres : `/`, `/metiers`, `/chantiers`, `/contact` |
| `router.php` | Routeur pour `php -S` en développement (inutile sous Apache, exclu de l'image) |
| `includes/config.php` | **Coordonnées, téléphones, menu, métiers, vidéos** : à modifier ici |
| `includes/header.php`, `includes/footer.php` | En-tête et pied de page communs |
| `includes/devis.php` | Traitement du formulaire de devis (SMTP) |
| `lib/PHPMailer/` | PHPMailer 6.9.3 (envoi SMTP), embarqué sans Composer |
| `css/style.css` | Styles (couleurs en variables dans `:root`) |
| `js/main.js` | Menu mobile, comparateur, filtres et visionneuse de la galerie, bouton haut de page |
| `img/` | Images (les vidéos sont sur MinIO, voir `VIDEO_BASE_URL`) |
| `storage/demandes.csv` | Copie de chaque demande de devis (non versionné, interdit au web) |
| `Dockerfile`, `.dockerignore` | Image de production (php:8.3-apache) |
| `source-maquette/`, `old/` | Maquette d'origine et ancien site : archives, **non publiées** (bloquées + exclues de l'image) |

## Formulaire de devis

Chaque demande est :
1. envoyée par **SMTP** à `CONTACT_TO` (Reply-To = e-mail du client, pour répondre directement) ;
2. ajoutée à `storage/demandes.csv` (filet de sécurité si l'envoi échoue).

Anti-spam : jeton CSRF, champ piège, délai minimal, 1 envoi/minute par session.
Si `SMTP_HOST` est vide, repli sur `mail()` (rarement disponible dans un conteneur).

### Variables d'environnement

| Variable | Exemple | Rôle |
|---|---|---|
| `SMTP_HOST` | `smtp.hostinger.com` | Serveur SMTP |
| `SMTP_PORT` | `587` | 587 (TLS) ou 465 (SSL) |
| `SMTP_SECURE` | `tls` | `tls`, `ssl` ou `none` |
| `SMTP_USER` | `site@babsbatsarl-ci.com` | Identifiant de la boîte d'envoi |
| `SMTP_PASS` | `••••••` | Mot de passe |
| `MAIL_FROM` | `site@babsbatsarl-ci.com` | Expéditeur : **doit être l'adresse de `SMTP_USER`** (sinon rejet / spam) |
| `MAIL_FROM_NAME` | `Site BAB'S-BAT` | Nom affiché |
| `CONTACT_TO` | `bas@babsbatsarl-ci.com` | Destinataire des demandes |

En local, créer `includes/config.local.php` (ignoré par git) :

```php
<?php return ['SMTP_HOST' => 'smtp…', 'SMTP_USER' => '…', 'SMTP_PASS' => '…', 'MAIL_FROM' => '…'];
```

## Déploiement Dokploy

1. **Create Application** → source **GitHub**, dépôt `babsbatweb`, branche `main`.
2. **Build Type** : `Dockerfile` (chemin `Dockerfile`, contexte `.`).
3. **Environment** : renseigner les variables ci-dessus.
4. **Volumes / Mounts** : volume nommé (ex. `babsbat-storage`) → `/var/www/html/storage`
   (sinon les demandes enregistrées sont perdues à chaque redéploiement).
5. **Domains** : `babsbatsarl-ci.com` (et `www`), port conteneur **80**, HTTPS activé (Let's Encrypt).
   Faire pointer le DNS (enregistrement A) vers l'IP du serveur Dokploy.
6. **Deploy**. Activer l'auto-deploy pour redéployer à chaque push sur `main`.

Vérifications après mise en ligne : envoyer une demande de devis test ; `/old/`, `/includes/config.php`
et `/storage/demandes.csv` doivent répondre **403** ; `/galerie.html` et `/metiers.php` doivent rediriger
vers `/chantiers` et `/metiers` ; une adresse inconnue doit afficher la page 404 « Brique manquante ».

## URL propres

Les adresses ne montrent jamais le nom du fichier. `.htaccess` réécrit `/metiers` vers `metiers.php`
(invisible pour le visiteur) et redirige en 301 toute adresse tapée en `.php` ou `.html` vers sa version propre.
Les liens du site sont relatifs, résolus par la balise `<base>` : ça marche à la racine comme sous XAMPP.

Pour ajouter une page : créer `ma-page.php`, puis ajouter `ma-page` aux règles `(metiers|chantiers|contact)`
du `.htaccess` et de `router.php`.

## Vidéos

Hébergées sur MinIO : `https://minio-files.kronyxtech.com/other.bucket/video/video-1.mp4` … `video-4.mp4`.
Liste et légendes dans `includes/config.php` (`VIDEOS`) ; dossier modifiable via la variable `VIDEO_BASE_URL`.
Le bucket MinIO doit être en lecture publique (policy `download`) et servir `Content-Type: video/mp4`.

## Branches

On travaille sur `dev`, puis on fusionne dans `main` une fois validé (Dokploy déploie `main`).

## Reste à compléter

- Noms des clients des témoignages (optionnel, dans `chantiers.php`, tableau `$testimonials`)
