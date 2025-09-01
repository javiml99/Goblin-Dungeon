<?php
/*
  Autor: Francisco Javier Muiños López
  Fecha de Última modificación: 09/11/2022
  Versión: 0.4
 */
?>
<?php

 function lerCSV($archivo)
        {
            $arrayDatos = [];
            if ($fp = fopen($archivo, 'r')) {
                //la función que hicimos al principio para leer el archivo csv
                while ($filaDatos = fgetcsv($fp, 0, ',')) {
                    $arrayDatos[] = $filaDatos;
                }
            } else {
                echo 'ERROR! No se puede acceder al fichero: ' .
                    $archivo .
                    '<br>';
                return false;
            }
            fclose($fp);
            return $arrayDatos;
        }

function escribirCSV($archivo, $arrayAEscribir)
    {
        //la función que usaremos al final para escribir en el archivo csv los valores del array datos
        if ($fp = fopen($archivo, 'w')) {
            foreach ($arrayAEscribir as $filaDatos) {
                fputcsv($fp, $filaDatos);
            }
        } else {
            echo 'ERROR! No se puede acceder al fichero: ' . $archivo . '<br>';
            return false;
        }
        fclose($fp);
        return true;
    }

function validarEmail($email)	//funciones de validacion
{
return false !== filter_var($email, FILTER_VALIDATE_EMAIL); 
}

function validarNombre($nombre)
{
return preg_match('/^[a-zA-Z]/', $nombre);
}

function validarC($contrasena)
{
return false !== preg_match('/^[a-zA-Z0-9_]/', $contrasena);
}

function validarCRep($contrasena2)
{
return false !== preg_match('/^[a-zA-Z0-9_]/', $contrasena2);
}

function validarFecha($fecha)
{
return preg_match('/[0-9]{1,2}\/[0-9]{1,2}\/[0-9][0-9][0-9][0-9]/', $fecha);
}


?>