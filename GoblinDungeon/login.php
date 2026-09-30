<?php
require_once __DIR__ . '/layout.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Dao::loginAllowed($_SERVER['REMOTE_ADDR'] ?? 'unknown')) {
        http_response_code(429);
        $error = 'Demasiados intentos. Espera 15 minutos antes de volver a probar.';
    } else {
        $user = Dao::findUser(trim(post('nombre')));
        // Valid dummy hash avoids a fast return when the user does not exist.
        $valid = password_verify(post('contrasena'), $user['password'] ?? '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.');
        if ($user && $valid) {
            session_regenerate_id(true);
            $_SESSION = ['usuario' => $user['name'], 'rol' => $user['role'], 'csrf' => bin2hex(random_bytes(32))];
            redirect('GoblinDungeon.php');
        }
        $error = 'Usuario o contraseña incorrectos.';
    }
}
page_start('Iniciar sesión');
?>
<section class="panel"><h1>Iniciar sesión</h1><p role="alert"><?= e($error) ?></p>
<form method="post" action="login.php"><?= csrf_field() ?>
<label>Usuario <input name="nombre" required autocomplete="username" maxlength="32"></label>
<label>Contraseña <input type="password" name="contrasena" required autocomplete="current-password" maxlength="72"></label>
<button>Entrar</button>
</form><p><a href="rexistro.php">Crear una cuenta</a></p>
<?php if (local_mode()): ?><p><a href="GoblinDungeon.php">Jugar como jugador local</a></p><?php endif; ?>
</section><?php page_end(); ?>
