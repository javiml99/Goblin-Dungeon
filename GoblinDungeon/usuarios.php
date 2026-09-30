<?php
require_once __DIR__ . '/layout.php';
require_admin();
page_start('Usuarios');
?>
<section class="panel"><h1>Usuarios</h1><table><tr><th>Usuario</th><th>Rol</th><th>Alta</th><th>Acción</th></tr>
<?php foreach (Dao::users() as $user): ?><tr>
<td><?= e($user['name']) ?></td><td><?= e($user['role']) ?></td><td><?= e($user['created_at']) ?></td>
<td><?php if ($user['role'] !== 'administrador'): ?><form action="borrar.php" method="post"><?= csrf_field() ?>
<input type="hidden" name="nombre" value="<?= e($user['name']) ?>"><button>Eliminar cuenta y victorias</button></form><?php endif; ?></td>
</tr><?php endforeach; ?></table></section><?php page_end(); ?>
