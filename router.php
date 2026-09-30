<?php
$path = rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/');
if (getenv('GOBLIN_LOCAL') === '1' && ($_SERVER['HTTP_HOST'] ?? '') !== '127.0.0.1:' . $_SERVER['SERVER_PORT']) {
    http_response_code(403); exit('Host no permitido.');
}
if ($path === '/__ready' && getenv('GOBLIN_READY_TOKEN')) {
    header('Content-Type: text/plain'); echo getenv('GOBLIN_READY_TOKEN'); return true;
}
$pages = ['index.php', 'GoblinDungeon.php', 'login.php', 'rexistro.php',
    'perfil.php', 'usuarios.php', 'borrar.php', 'cerrar.php', 'avatar.php'];
if ($path === '/') $path = '/index.php';
if (in_array(ltrim($path, '/'), $pages, true) && substr_count($path, '/') === 1) {
    require __DIR__ . '/GoblinDungeon' . $path;
    return true;
}
if (preg_match('~^/(imx/[a-zA-Z0-9+_.-]+\.(png|jpg)|estilos/[a-zA-Z0-9_-]+\.css)$~D', $path)
    && is_file(__DIR__ . '/GoblinDungeon' . $path)) return false;
http_response_code(404);
echo 'No encontrado.';
