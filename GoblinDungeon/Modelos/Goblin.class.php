<?php
  

  class Goblin {
    public $nombreGoblin;
    public $vidaGoblin;
    public $ataqueGoblin;
    public $imagen;

    public function __construct($nombreGoblin,$vidaGoblin, $ataqueGoblin, $imagen)
    {
      $this->nombreGoblin = $nombreGoblin;
    $this->vidaGoblin = $vidaGoblin;
    $this->ataqueGoblin = $ataqueGoblin; 
    $this->imagen = $imagen;  
    
    }
    
    public function getNombreGoblin()
  {
    return $this->nombreGoblin;
  }
  public function getVidaGoblin()
  {
    return $this->vidaGoblin;
  }
  public function getAtaqueGoblin()
  {
    return $this->ataqueGoblin;
  }
  public function getImagen()
  {
    return $this->imagen;
  }

public function setVidaGoblin($vidaGoblin){
  $this->vidaGoblin = $vidaGoblin;
}
  }
