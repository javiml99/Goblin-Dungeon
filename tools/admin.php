<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require_once __DIR__ . '/../GoblinDungeon/Modelos/DAO.class.php';
$name = $argv[1] ?? '';
if (!Dao::findUser($name)) { fwrite(STDERR, "Primero registra esa cuenta desde el juego.\n"); exit(1); }
Dao::db()->prepare("UPDATE users SET role = 'administrador' WHERE name = ?")->execute([$name]);
echo "Cuenta promovida a administrador. Cierra sesión y vuelve a entrar.\n";
