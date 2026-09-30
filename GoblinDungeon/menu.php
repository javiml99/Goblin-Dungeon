<?php require_once __DIR__ . '/bootstrap.php'; ?>
<nav aria-label="Menú principal">
<a href="GoblinDungeon.php"><img src="imx/logo.png" width="48" height="48" alt=""> Goblin Dungeon</a>
<a href="GoblinDungeon.php">Jugar</a>
<?php if (isset($_SESSION['usuario'])): ?>
<span><?= e(!empty($_SESSION['guest']) ? 'Jugador local' : $_SESSION['usuario']) ?></span>
<a href="perfil.php">Perfil</a>
<?php if (($_SESSION['rol'] ?? '') === 'administrador'): ?><a href="usuarios.php">Usuarios</a><?php endif; ?>
<?php if (empty($_SESSION['guest'])): ?>
<form method="post" action="cerrar.php"><?= csrf_field() ?><button>Cerrar sesión</button></form>
<?php else: ?><a href="login.php">Usar una cuenta</a><?php endif; ?>
<?php else: ?><a href="login.php">Iniciar sesión</a><a href="rexistro.php">Registrarse</a><?php endif; ?>
</nav>
