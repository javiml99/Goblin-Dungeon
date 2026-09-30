<?php
declare(strict_types=1);
require_once __DIR__ . '/Modelos/Partida.class.php';
require_once __DIR__ . '/Modelos/DAO.class.php';

function e($value): string {
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
function redirect(string $path): never {
    header('Location: ' . $path, true, 303);
    exit;
}
function post(string $key): string {
    return isset($_POST[$key]) && is_string($_POST[$key]) ? $_POST[$key] : '';
}
function csrf_field(): string {
    return '<input type="hidden" name="csrf" value="' . e($_SESSION['csrf']) . '">';
}
function require_login(): void {
    if (!isset($_SESSION['usuario'])) redirect('login.php');
}
function require_admin(): void {
    require_login();
    $user = Dao::findUser($_SESSION['usuario']);
    if (!$user || $user['role'] !== 'administrador') {
        http_response_code(403);
        exit('Acceso reservado al administrador.');
    }
}
function local_mode(): bool {
    return getenv('GOBLIN_LOCAL') === '1';
}

if (PHP_SAPI !== 'cli') {
    // Set paths through PHP's API: CLI -d values are parsed as INI syntax and
    // Windows short names (RUNNER~1), spaces and punctuation can break them.
    if ($sessionDir = getenv('GOBLIN_SESSION_DIR')) {
        session_save_path($sessionDir);
    }
    if ($errorLog = getenv('GOBLIN_ERROR_LOG')) {
        ini_set('error_log', $errorLog);
    }
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    session_name('goblin_session');
    session_set_cookie_params([
        'lifetime' => local_mode() ? 2592000 : 0,
        'path' => '/',
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
    $_SESSION['csrf'] ??= bin2hex(random_bytes(32));
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('Referrer-Policy: same-origin');
    header("Content-Security-Policy: default-src 'self'; img-src 'self'; style-src 'self' 'unsafe-inline'; form-action 'self'; frame-ancestors 'none'; base-uri 'none'");
    header('Cache-Control: no-store');
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST'
        && !hash_equals($_SESSION['csrf'], post('csrf'))) {
        http_response_code(403);
        exit('El formulario ha caducado. Recarga la página e inténtalo de nuevo.');
    }
}

if (PHP_SAPI !== 'cli' && isset($_SESSION['usuario']) && empty($_SESSION['guest'])) {
    if (!Dao::findUser($_SESSION['usuario'])) {
        $_SESSION = [];
        session_regenerate_id(true);
        redirect('login.php');
    }
}
