<?php
require_once __DIR__ . '/layout.php';
require_login();
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['preferencias'])) {
        $theme = post('estilo');
        $font = post('fuenteest');
        $size = filter_var(post('fuente'), FILTER_VALIDATE_INT);
        if (in_array($theme, ['maquetacio', 'gatico', 'pescao'], true)
            && in_array($font, ['Helvetica', 'sans-serif'], true) && $size >= 12 && $size <= 48) {
            $_SESSION['theme'] = $theme;
            $_SESSION['font'] = $font;
            $_SESSION['font_size'] = $size;
            redirect('perfil.php');
        }
        $error = 'Revisa las preferencias.';
    } elseif (isset($_POST['avatar'])) {
        $file = $_FILES['image'] ?? [];
        $tmp = $file['tmp_name'] ?? '';
        $info = is_uploaded_file($tmp) ? @getimagesize($tmp) : false;
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK || !$info
            || $info[2] !== IMAGETYPE_PNG || ($file['size'] ?? 0) > 200000
            || $info[0] > 1024 || $info[1] > 1024) {
            $error = 'Escoge un PNG de hasta 200 KB y 1024 × 1024 píxeles.';
        } else {
            $dir = Dao::dataDir() . '/avatars';
            if (!is_dir($dir)) mkdir($dir, 0700, true);
            $dest = $dir . '/' . hash('sha256', strtolower($_SESSION['usuario'])) . '.png';
            if (!move_uploaded_file($tmp, $dest)) $error = 'No se pudo guardar la imagen.';
            else redirect('perfil.php');
        }
    }
}
page_start('Perfil');
?>
<section class="panel"><h1>Tu perfil</h1><p role="alert"><?= e($error) ?></p>
<img src="avatar.php" width="100" height="100" alt="Tu avatar">
<form action="perfil.php" method="post" enctype="multipart/form-data"><?= csrf_field() ?>
<label>Avatar PNG (máximo 200 KB) <input type="file" name="image" accept="image/png" required></label><button name="avatar" value="1">Guardar avatar</button></form>
<h2>Preferencias</h2><form method="post" action="perfil.php"><?= csrf_field() ?>
<label>Tema <select name="estilo">
<?php foreach (['maquetacio' => 'Original', 'gatico' => 'Gatico', 'pescao' => 'Pescao'] as $value => $label): ?>
<option value="<?= e($value) ?>" <?= ($_SESSION['theme'] ?? 'maquetacio') === $value ? 'selected' : '' ?>><?= e($label) ?></option><?php endforeach; ?>
</select></label>
<label>Tamaño de letra <input type="number" name="fuente" min="12" max="48" value="<?= e($_SESSION['font_size'] ?? 20) ?>" required></label>
<label>Fuente <select name="fuenteest"><option value="sans-serif">Sans serif</option><option value="Helvetica" <?= ($_SESSION['font'] ?? '') === 'Helvetica' ? 'selected' : '' ?>>Helvetica</option></select></label>
<button name="preferencias" value="1">Guardar preferencias</button></form>
<h2>Tus personajes ganadores</h2><div class="table-scroll"><table><tr><th>Nombre</th><th>Clase</th><th>Vida</th><th>Ataque</th><th>Defensa</th><th>Esquiva</th><th>Inventario</th></tr>
<?php foreach (Dao::wins($_SESSION['usuario']) as $win): ?><tr><?php foreach ($win as $value): ?><td><?= e(is_array($value) ? implode(', ', $value) : $value) ?></td><?php endforeach; ?></tr><?php endforeach; ?>
</table></div><p>Una partida en curso se conserva mientras siga disponible tu sesión. Las victorias se guardan al pulsar «Guardar victoria y volver».</p></section>
<?php page_end(); ?>
