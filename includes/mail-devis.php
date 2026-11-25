<?php
/**
 * Modèle de l'e-mail « Nouvelle demande de devis » envoyé à CONTACT_TO.
 *
 * HTML pensé pour les messageries (Gmail, Outlook, Apple Mail, mobile) :
 * mise en page en tableaux, styles en ligne, couleurs unies du site, aucun dégradé.
 * Les polices du site (Barlow) s'affichent là où la messagerie les accepte, sinon Arial.
 */
require_once __DIR__ . '/config.php';

/** Numéro WhatsApp international (chiffres seuls) déduit du téléphone saisi, ou '' si impossible. */
function devis_whatsapp_number(string $tel): string
{
    $digits = preg_replace('/\D+/', '', $tel);
    if (str_starts_with($digits, '00')) {
        $digits = substr($digits, 2);
    }
    if (strlen($digits) === 10 && $digits[0] === '0') {
        $digits = '225' . $digits;            // numéro ivoirien saisi sans indicatif
    }
    return strlen($digits) >= 11 && strlen($digits) <= 15 ? $digits : '';
}

/** Lien tel: propre à partir de la saisie du client. */
function devis_tel_href(string $tel): string
{
    $t = preg_replace('/[^\d+]/', '', $tel);
    return 'tel:' . $t;
}

/** Objet de l'e-mail (sans tiret cadratin). */
function devis_mail_subject(array $d): string
{
    $nom = trim(preg_replace('/[\r\n]+/', ' ', $d['nom']));
    return 'Nouvelle demande de devis : ' . SERVICES[$d['service']] . ', ' . $nom;
}

/** Version texte (messageries sans HTML, aperçu des notifications). */
function devis_mail_text(array $d, string $when): string
{
    $line = static fn(string $v): string => trim(preg_replace('/[\r\n]+/', ' ', $v));
    $none = 'Non renseigné';
    return "NOUVELLE DEMANDE DE DEVIS\n"
        . "Reçue le $when via le formulaire du site " . SITE['domain'] . ".\n\n"
        . 'Métier demandé : ' . SERVICES[$d['service']] . "\n"
        . 'Client : ' . $line($d['nom']) . "\n"
        . 'Téléphone : ' . $line($d['tel']) . "\n"
        . 'E-mail : ' . ($d['mail'] !== '' ? $d['mail'] : $none) . "\n"
        . 'Lieu du projet : ' . ($line($d['lieu']) !== '' ? $line($d['lieu']) : $none) . "\n"
        . 'Budget envisagé : ' . $d['budget'] . "\n\n"
        . "Message du client :\n" . ($d['msg'] !== '' ? $d['msg'] : $none) . "\n\n"
        . "Pour répondre au client, utilisez le bouton Répondre de votre messagerie.\n"
        . "Cette demande est également enregistrée sur le serveur du site.\n\n"
        . SITE['legal'] . "\n" . SITE['address'] . "\n" . PHONES['main'][0] . "\n";
}

/**
 * Version HTML.
 * $logoSrc : 'cid:logo' (image jointe au mail) ou une URL.
 */
function devis_mail_html(array $d, string $when, string $logoSrc = 'cid:logo'): string
{
    $h = static fn(string $v): string => htmlspecialchars($v, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $line = static fn(string $v): string => trim(preg_replace('/[\r\n]+/', ' ', $v));

    // Couleurs du site
    $ink = '#16110D'; $paper = '#F3EDE4'; $paper2 = '#FBF8F3'; $sand = '#E7DDCF';
    $brick = '#B5451B'; $brickDark = '#9A3A15'; $orange = '#F39200'; $green = '#00742A'; $muted = '#5A4C42';
    $font  = "Barlow, 'Segoe UI', Arial, Helvetica, sans-serif";
    $label = "'Barlow Condensed', 'Arial Narrow', Arial, Helvetica, sans-serif";

    $service = $h(SERVICES[$d['service']]);
    $nom     = $h($line($d['nom']));
    $tel     = $h($line($d['tel']));
    $none    = '<span style="color:' . $muted . ';">Non renseigné</span>';
    $mail    = $d['mail'] !== ''
        ? '<a href="mailto:' . $h($d['mail']) . '" style="color:' . $green . '; text-decoration:underline;">' . $h($d['mail']) . '</a>'
        : $none;
    $lieu    = $line($d['lieu']) !== '' ? $h($line($d['lieu'])) : $none;
    $budget  = $h($d['budget']);
    $msg     = $d['msg'] !== '' ? nl2br($h($d['msg'])) : $none;
    $wa      = devis_whatsapp_number($d['tel']);
    $preview = $service . ', ' . $nom . ', ' . $tel;

    // Une ligne du tableau de détails
    $row = static function (string $k, string $v, bool $last = false) use ($label, $font, $ink, $muted, $sand): string {
        $border = $last ? '' : 'border-bottom:1px solid ' . $sand . ';';
        return '<tr>'
            . '<td width="140" valign="top" style="padding:12px 0; ' . $border . ' font-family:' . $label . '; font-size:13px; font-weight:600; letter-spacing:1px; text-transform:uppercase; color:' . $muted . ';">' . $k . '</td>'
            . '<td valign="top" style="padding:12px 0; ' . $border . ' font-family:' . $font . '; font-size:16px; line-height:1.45; color:' . $ink . ';">' . $v . '</td>'
            . '</tr>';
    };

    // Bouton (table pour compatibilité Outlook)
    $button = static function (string $href, string $text, string $bg, string $color) use ($font): string {
        return '<td class="btn-cell" style="padding:0 8px 8px 0;">'
            . '<table role="presentation" class="btn-table" cellpadding="0" cellspacing="0" border="0"><tr>'
            . '<td class="btn-bg" bgcolor="' . $bg . '" style="background:' . $bg . ';">'
            . '<a href="' . $href . '" style="display:inline-block; padding:13px 20px; font-family:' . $font . '; font-size:15px; font-weight:700; color:' . $color . '; text-decoration:none;">' . $text . '</a>'
            . '</td></tr></table></td>';
    };

    $buttons = $button($h(devis_tel_href($d['tel'])), 'Appeler le client', $ink, '#FFFFFF');
    if ($wa !== '') {
        $buttons .= $button('https://wa.me/' . $wa, 'WhatsApp', $green, '#FFFFFF');
    }
    if ($d['mail'] !== '') {
        $buttons .= $button('mailto:' . $h($d['mail']) . '?subject=' . rawurlencode('Votre demande de devis ' . SITE['legal']), 'Répondre par e-mail', $orange, $ink);
    }

    $rows = $row('Client', '<strong>' . $nom . '</strong>')
          . $row('Téléphone', '<a href="' . $h(devis_tel_href($d['tel'])) . '" style="color:' . $ink . '; text-decoration:none; font-weight:600;">' . $tel . '</a>')
          . $row('E-mail', $mail)
          . $row('Lieu du projet', $lieu)
          . $row('Budget envisagé', $budget, true);

    $site    = $h(SITE['legal']);
    $slogan  = $h(SITE['slogan']);
    $domain  = $h(SITE['domain']);
    $address = $h(SITE['address']);
    $phone   = $h(PHONES['main'][0]);
    $whenH   = $h($when);

    return <<<HTML
<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="x-apple-disable-message-reformatting">
<title>Nouvelle demande de devis</title>
<link href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;600;700&amp;family=Barlow+Condensed:wght@600;700&amp;display=swap" rel="stylesheet">
<style>
  @media (max-width: 620px) {
    .container { width: 100% !important; }
    .px { padding-left: 20px !important; padding-right: 20px !important; }
    .btn-cell { display: block !important; width: 100% !important; padding: 0 0 8px 0 !important; }
    .btn-table { width: 100% !important; }
    .btn-bg a { display: block !important; text-align: center !important; }
  }
</style>
</head>
<body style="margin:0; padding:0; background:{$paper};">
<div style="display:none; max-height:0; overflow:hidden; mso-hide:all;">{$preview}</div>
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="{$paper}" style="background:{$paper};">
  <tr>
    <td align="center" style="padding:24px 12px;">
      <table role="presentation" class="container" width="600" cellpadding="0" cellspacing="0" border="0" style="width:600px; max-width:600px; background:#FFFFFF; border:1px solid {$sand};">

        <!-- En-tête -->
        <tr>
          <td class="px" style="padding:22px 32px; background:{$paper2};">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
              <tr>
                <td width="66" valign="middle"><img src="{$logoSrc}" width="58" height="50" alt="" style="display:block; border:0;"></td>
                <td valign="middle" style="font-family:{$label}; color:{$ink};">
                  <div style="font-size:20px; font-weight:700; letter-spacing:.5px; text-transform:uppercase; line-height:1.1;">{$site}</div>
                  <div style="font-family:{$font}; font-size:13px; color:{$muted}; line-height:1.4;">{$slogan}</div>
                </td>
              </tr>
            </table>
          </td>
        </tr>
        <tr><td height="4" bgcolor="{$brick}" style="height:4px; line-height:4px; font-size:0; background:{$brick};">&nbsp;</td></tr>

        <!-- Titre -->
        <tr>
          <td class="px" style="padding:30px 32px 8px;">
            <div style="font-family:{$label}; font-size:13px; font-weight:700; letter-spacing:1.5px; text-transform:uppercase; color:{$brickDark};">Formulaire de contact</div>
            <h1 style="margin:6px 0 8px; font-family:{$font}; font-size:24px; line-height:1.25; font-weight:700; color:{$ink};">Nouvelle demande de devis</h1>
            <p style="margin:0; font-family:{$font}; font-size:15px; line-height:1.5; color:{$muted};">Reçue le {$whenH} via le site {$domain}.</p>
          </td>
        </tr>

        <!-- Métier demandé -->
        <tr>
          <td class="px" style="padding:18px 32px 6px;">
            <table role="presentation" cellpadding="0" cellspacing="0" border="0"><tr>
              <td bgcolor="{$orange}" style="background:{$orange}; padding:8px 14px; font-family:{$label}; font-size:15px; font-weight:700; letter-spacing:1px; text-transform:uppercase; color:{$ink};">{$service}</td>
            </tr></table>
          </td>
        </tr>

        <!-- Détails -->
        <tr>
          <td class="px" style="padding:10px 32px 8px;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">{$rows}</table>
          </td>
        </tr>

        <!-- Message -->
        <tr>
          <td class="px" style="padding:14px 32px 8px;">
            <div style="font-family:{$label}; font-size:13px; font-weight:600; letter-spacing:1px; text-transform:uppercase; color:{$muted}; margin-bottom:8px;">Message du client</div>
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"><tr>
              <td style="background:{$paper2}; border-left:4px solid {$brick}; padding:16px 18px; font-family:{$font}; font-size:16px; line-height:1.6; color:{$ink};">{$msg}</td>
            </tr></table>
          </td>
        </tr>

        <!-- Actions -->
        <tr>
          <td class="px" style="padding:20px 32px 26px;">
            <table role="presentation" cellpadding="0" cellspacing="0" border="0"><tr>{$buttons}</tr></table>
          </td>
        </tr>

        <!-- Note -->
        <tr>
          <td class="px" style="padding:16px 32px 24px; border-top:1px solid {$sand}; font-family:{$font}; font-size:13px; line-height:1.6; color:{$muted};">
            Ce message a été envoyé automatiquement depuis le formulaire de contact du site.
            Pour répondre au client, utilisez le bouton Répondre de votre messagerie : la réponse lui sera adressée directement.
            La demande est également enregistrée sur le serveur du site.
          </td>
        </tr>
      </table>

      <!-- Pied -->
      <table role="presentation" class="container" width="600" cellpadding="0" cellspacing="0" border="0" style="width:600px; max-width:600px;">
        <tr>
          <td class="px" style="padding:16px 32px; font-family:{$font}; font-size:12px; line-height:1.6; color:{$muted}; text-align:center;">
            <strong style="color:{$ink};">{$site}</strong><br>
            {$address}<br>
            {$phone} · {$domain}
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
</body>
</html>
HTML;
}
