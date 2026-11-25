<?php
/**
 * Informations de l'entreprise — modifier ici, cela se répercute sur tout le site.
 */

const SITE = [
    'name'     => "BAB'S-BAT",
    'legal'    => "BAB'S-BAT SARL",
    'slogan'   => 'Bâtissons ensemble les maisons de demain',
    'domain'   => 'babsbatsarl-ci.com',
    'url'      => 'https://babsbatsarl-ci.com',
    'email'    => 'bas@babsbatsarl-ci.com',
    'address'  => "Abidjan Cocody Riviera Faya, bd Mitterrand, face à l'église orthodoxe, au-dessus de la banque BOA",
    'po_box'   => 'BP 482 Bingerville',
    'manager'  => 'Aimé Stéphane BABO',
    'hours'    => ['Lundi – vendredi : 8 h – 17 h', 'Samedi : 9 h – 12 h'],
    'map_query'=> 'Riviera Faya, Boulevard Mitterrand, Abidjan',
];

/** Téléphones : [affichage, format international pour tel:] */
const PHONES = [
    'main'   => ['(+225) 07 88 37 37 37', '+2250788373737'],
    'mobile' => ['(+225) 07 07 90 08 90', '+2250707900890'],
    'fixe'   => ['(+225) 27 22 28 96 69', '+2252722289669'],
];

const WHATSAPP = 'https://wa.me/2250788373737';

/** Adresse qui reçoit les demandes de devis. */
const CONTACT_TO = 'bas@babsbatsarl-ci.com';

/** Pages du menu principal : fichier => libellé */
const NAV = [
    'index.php'     => 'Accueil',
    'metiers.php'   => 'Métiers',
    'chantiers.php' => 'Chantiers',
];

/** Les métiers proposés dans le formulaire de devis. */
const SERVICES = [
    'construction' => 'Construction',
    'villa-songon' => 'Villa à Songon',
    'lotissement'  => 'Lotissement',
    'location'     => 'Location-vente',
    'gestion'      => 'Gestion immobilière',
    'securite'     => 'Vidéosurveillance',
    'solaire'      => 'Solaire',
    'autre'        => 'Autre',
];

const BUDGETS = [
    'Je ne sais pas encore',
    'Moins de 30 millions FCFA',
    '30 à 75 millions FCFA',
    'Plus de 75 millions FCFA',
];

/** Échappement HTML court. */
function e(?string $s): string
{
    return htmlspecialchars((string) $s, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}
