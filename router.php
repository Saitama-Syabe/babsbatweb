<?php
/**
 * Routeur pour le serveur de développement PHP uniquement :
 *   php -S 127.0.0.1:8000 router.php
 * Reproduit les URL propres du .htaccess (Apache n'en a pas besoin).
 */
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if (preg_match('#^/(includes|storage|lib|old|bin|source-maquette)(/|$)#i', $path) || preg_match('#\.md$#i', $path)) {
    http_response_code(403);
    exit('Accès interdit');
}
if (preg_match('#^/(index|metiers|chantiers|contact)\.php$#', $path, $m)) {
    header('Location: /' . ($m[1] === 'index' ? '' : $m[1]) . (empty($_SERVER['QUERY_STRING']) ? '' : '?' . $_SERVER['QUERY_STRING']), true, 301);
    exit;
}
if (preg_match('#^/(metiers|chantiers|contact)/?$#', $path, $m)) {
    $_SERVER['SCRIPT_NAME'] = '/' . $m[1] . '.php';
    require __DIR__ . '/' . $m[1] . '.php';
    return true;
}
if ($path === '/') {
    $_SERVER['SCRIPT_NAME'] = '/index.php';
    require __DIR__ . '/index.php';
    return true;
}
if (is_file(__DIR__ . $path)) {
    return false; // fichier statique (css, js, images)
}
$_SERVER['SCRIPT_NAME'] = '/404.php';
require __DIR__ . '/404.php';
