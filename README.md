# babsbatweb — site vitrine BAB'S-BAT SARL

Site PHP sans framework ni build : il tourne tel quel sous XAMPP / Apache (PHP 8+).

- Local : http://localhost/personnalProject/babsbatweb/
- Ou : `php -S 127.0.0.1:8000` à la racine, puis http://127.0.0.1:8000

## Structure

| Chemin | Rôle |
|---|---|
| `index.php`, `metiers.php`, `chantiers.php`, `contact.php` | Les 4 pages |
| `includes/config.php` | **Coordonnées, téléphones, menu, métiers du formulaire** : à modifier ici |
| `includes/header.php`, `includes/footer.php` | En-tête et pied de page communs |
| `includes/devis.php` | Traitement du formulaire de devis |
| `css/style.css` | Styles (couleurs en variables dans `:root`) |
| `js/main.js` | Comparateur plan/chantier, filtres de la galerie |
| `img/`, `video/` | Médias |
| `storage/demandes.csv` | Copie de chaque demande de devis (non versionné, non accessible par le web) |
| `source-maquette/` | Fichiers d'origine de la maquette (référence) |
| `old/` | Ancienne version du site (archive) |

## Formulaire de devis

Envoi par `mail()` vers `CONTACT_TO` (`includes/config.php`), et chaque demande est aussi ajoutée à
`storage/demandes.csv`. En local (XAMPP), `mail()` n'est généralement pas configuré : les demandes
restent consultables dans le CSV. Anti-spam : jeton CSRF, champ piège, délai minimal, 1 envoi/minute.

## Branches

On travaille sur `dev`, puis on fusionne dans `main` une fois validé.

## Reste à compléter (contenu client)

Recherche : `grep -rn "\[" *.php` : les éléments entre crochets `[ … ]`.

- Chiffres clés (années d'expérience, maisons livrées, briques produites)
- Mot du gérant
- Témoignage client
- Légendes des 4 vidéos
- Lotissement : surface et prix des lots
- Location-vente : apport et durée
- Délai de rappel après une demande de devis (actuellement « [48 h] »)
