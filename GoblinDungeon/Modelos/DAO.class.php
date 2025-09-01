<?php
/*
  Autor: Francisco Javier Muiños López
  Fecha de Última modificación: 21/11/2022
  Versión: 0.9
 */
?>
<?php
include("Usuario.class.php");
include_once("Xogador.class.php");
class Dao
{
    public $rutaFicheros;

    static function obtenerUsuarios($archivo)
    {
        $arrayDatos = [];
        if ($fp = fopen($archivo, 'r')) {
            //la función que hicimos al principio para leer el archivo csv
            while ($filaDatos = fgetcsv($fp, 0, ',')) {
                $usuario = new Usuario($filaDatos[0], $filaDatos[1], $filaDatos[2], $filaDatos[3], $filaDatos[4]);
                $arrayDatos[] = $usuario;
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

    static function escribirUsuario($archivo, $arrayAEscribir)
    {
        if ($fp = fopen($archivo, 'w')) {
            foreach ($arrayAEscribir as $usuario) {
                $filaDatos = [$usuario->getRol(),$usuario->getNombre(), $usuario->getContrasena(), $usuario->getEmail(), $usuario->getFecha()];
                fputcsv($fp, $filaDatos);
            }
        } else {
            echo 'ERROR! No se puede acceder al fichero: ' . $archivo . '<br>';
            return false;
        }
        fclose($fp);
        return true;
    }

    static function leerPersonaje($archivo)
    {
        $arrayDatos = [];
        if ($fp = fopen($archivo, 'r')) {
            //la función que hicimos al principio para leer el archivo csv
            while ($filaDatos = fgetcsv($fp, 0, ',')) {
                $xogador = new Xogador($filaDatos[0], $filaDatos[1], $filaDatos[2], $filaDatos[3], $filaDatos[4], $filaDatos[5], $filaDatos[6]);
                $arrayDatos[] = $xogador;
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

    static function escribirPersonaje($archivo, $arrayAEscribir)
    {
        if ($fp = fopen($archivo, 'w')) {                       
            foreach ($arrayAEscribir as $xogador) {     
                if(is_array($xogador->getInventario())){
                    $xogador->setInventario(implode(",", $xogador->getInventario())); //Para que no haya errores, se comprueba si el inventario es un array y si lo es, se transforma a string
                } else {}                                                                                
                $filaDatos = [$xogador->getNombre(),$xogador->getClase(), $xogador->getVida(), $xogador->getAtaque(), $xogador->getDefensa(), $xogador->getEsquiva(), $xogador->getInventario()];
                fputcsv($fp, $filaDatos);
            }
        } else {
            echo 'ERROR! No se puede acceder al fichero: ' . $archivo . '<br>';
            return false;
        }
        fclose($fp);
        return true;
    }

    
}


?>