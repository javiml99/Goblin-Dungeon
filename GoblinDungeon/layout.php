<?php
require_once __DIR__ . '/bootstrap.php';
function page_start(string $title): void {
    $theme = $_SESSION['theme'] ?? 'maquetacio';
    if (!in_array($theme, ['maquetacio', 'gatico', 'pescao'], true)) $theme = 'maquetacio';
    $size = max(12, min(48, (int) ($_SESSION['font_size'] ?? 20)));
    $font = ($_SESSION['font'] ?? '') === 'Helvetica' ? 'Helvetica' : 'sans-serif';
    ?><!doctype html>
<html lang="es"><head><meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title) ?> · Goblin Dungeon</title>
<link rel="stylesheet" href="estilos/<?= e($theme) ?>.css">
<link rel="stylesheet" href="estilos/responsive.css">
<style>body { font-size: <?= $size ?>px; font-family: <?= $font ?>; }</style>
</head><body><?php require __DIR__ . '/menu.php'; ?>
<main><?php
}
function page_end(): void {
    ?></main><footer>Goblin Dungeon · Francisco Javier Muiños López (ElJabi)</footer></body></html><?php
}
