<?php
require_once __DIR__ . '/bootstrap.php';
require_login();
$path = Dao::dataDir() . '/avatars/' . hash('sha256', strtolower($_SESSION['usuario'])) . '.png';
header('Content-Type: ' . (is_file($path) ? 'image/png' : 'image/jpeg'));
readfile(is_file($path) ? $path : __DIR__ . '/imx/default.jpg');
