<?php
/*
  Autor: Francisco Javier Muiños López
  Fecha de Última modificación: 21/11/2022
  Versión: 0.9
 */

class Usuario
{
  private $rol;
  private $nombre;
  private $contrasena;
  private $email;
  private $fecha;
  

  public function __construct($rol, $nombre, $contrasena, $email, $fecha)
  {
    $this->rol = $rol;
    $this->nombre = $nombre;
    $this->contrasena = $contrasena;
    $this->email = $email;
    $this->fecha = $fecha; 
  }

  public function esAdmin()
  {
    if ($this->rol === 'administrador') {
      return true;
    } else {
      return false;
    }
  }

  public function getRol()
  {
    return $this->rol;
  }

  public function getNombre()
  {
    return $this->nombre;
  }

  public function getContrasena()
  {
    return $this->contrasena;
  }

  public function getEmail()
  {
    return $this->email;
  }


  public function getFecha()
  {
    return $this->fecha;
  }

  

  

  
}
