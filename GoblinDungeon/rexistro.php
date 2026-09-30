<?php
require_once __DIR__ . '/layout.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim(post('nombre'));
    $password = post('contrasena');
    $email = trim(post('email'));
    if (!preg_match('/^[a-zA-Z0-9][a-zA-Z0-9_-]{2,31}$/D', $name)) {
        $error = 'El usuario debe tener entre 3 y 32 caracteres: letras, números, guion o guion bajo.';
    } elseif (strlen($password) < 8 || strlen($password) > 72) {
        $error = 'La contraseña debe tener entre 8 y 72 bytes.';
    } elseif ($password !== post('contrasena2')) {
        $error = 'Las contraseñas no coinciden.';
    } elseif ($email !== '' && (strlen($email) > 254 || !filter_var($email, FILTER_VALIDATE_EMAIL))) {
        $error = 'El correo no es válido.';
    } elseif (!Dao::register($name, $password, $email)) {
        $error = 'Ese nombre de usuario ya existe.';
    } else {
        session_regenerate_id(true);
        $_SESSION = ['usuario' => $name, 'rol' => 'usuario', 'csrf' => bin2hex(random_bytes(32))];
        redirect('GoblinDungeon.php');
    }
}
page_start('Crear cuenta');
?>
<section class="panel"><h1>Crear cuenta</h1><p role="alert"><?= e($error) ?></p>
<form method="post" action="rexistro.php"><?= csrf_field() ?>
<label>Usuario <input name="nombre" required minlength="3" maxlength="32" autocomplete="username" value="<?= e(post('nombre')) ?>"></label>
<label>Contraseña <input type="password" name="contrasena" required minlength="8" maxlength="72" autocomplete="new-password"></label>
<label>Repite la contraseña <input type="password" name="contrasena2" required minlength="8" maxlength="72" autocomplete="new-password"></label>
<label>Correo (opcional) <input type="email" name="email" maxlength="254" value="<?= e(post('email')) ?>"></label>
<button>Registrarse</button>
</form><p>Las cuentas de Windows se guardan en tu ordenador. No se sincronizan con una futura web.</p></section>
<?php page_end(); ?>
