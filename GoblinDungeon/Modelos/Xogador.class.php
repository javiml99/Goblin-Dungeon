<?php
/*
  Autor: Francisco Javier Muiños López
  Fecha de Última modificación: 21/11/2022
  Versión: 0.9
 */

class Xogador
{
  private $nombre; 
  private $clase;
  private $vida;
  private $ataque;
  private $defensa;
  private $esquiva; 
  private $inventario; 

  

  public function __construct($nombre, $clase, $vida, $ataque, $defensa, $esquiva, $inventario)
  {   
    $this->nombre = $nombre;
    $this->clase = $clase;
    $this->vida = $vida;
    $this->ataque = $ataque;
    $this->defensa = $defensa;
    $this->esquiva = $esquiva;
    $this->inventario = $inventario;   
  }

  

  public function getNombre()
  {
    return $this->nombre;
  }

  public function getClase()
  {
    return $this->clase;
  }

  public function getVida()
  {
    return $this->vida;
  }

  public function setVida($vida){
    $this->vida= $vida;
  }

  public function getAtaque()
  {
    return $this->ataque;
  }

  public function setAtaque($ataque){
    $this->ataque= $ataque;
  }

  public function getDefensa()
  {
    return $this->defensa;
  }

  public function setDefensa($defensa){
    $this->defensa= $defensa;
  }

  public function getEsquiva()
  {
    return $this->esquiva;
  }

  public function setEsquiva($esquiva){
    $this->esquiva= $esquiva;
  }

  public function getinventario()
  {
    return $this->inventario;
  }

  public function setInventario($inventario){
    $this->inventario= $inventario;
  }


  public function conseguirObjeto($inventario, $objeto)
  {
    array_push($inventario, $objeto);
    $_SESSION['xogador'][0]->setInventario($inventario);
  }
  
}
