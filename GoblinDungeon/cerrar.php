<?php
/*
  Autor: Francisco Javier Muiños López
  Fecha de Última modificación: 15/11/2022
  Versión: 0.89989928982995
 */
?>
<?php
session_start();
$_SESSION=array();
session_unset(); 
session_destroy();
header('Location: login.php');
 ?>