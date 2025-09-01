<?php
/*
  Autor: Francisco Javier Muiños López
  Fecha de Última modificación: 14/11/2022
  Versión: 0.89989928982996
 */
?>
<?php
include 'funciones.php';
$archivo = 'csv/autenticacion2.csv';
$datos = lerCSV($archivo);
unset($datos[$_GET['id']]);
escribirCSV($archivo, $datos);
header('Location: usuarios.php');   
?>