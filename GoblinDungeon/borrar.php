<?php
require_once __DIR__ . '/bootstrap.php';
require_admin();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); header('Allow: POST'); exit; }
Dao::deleteUser(post('nombre'));
redirect('usuarios.php');
