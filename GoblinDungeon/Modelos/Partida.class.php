<?php
require_once __DIR__ . "/Xogador.class.php";
require_once __DIR__ . "/Goblin.class.php";

class Partida
{
  public $turno;
  public $evento;

  public function __construct($turno, $evento)
  {
    $this->turno = $turno;
    $this->evento = $evento;
  }


  public function controladorTurnos($turno)   //Con este método se controla en que parte de la partida se encuentra el jugador, creando nuevos goblins cuando los anteriores son derrotados
  {
    switch ($turno) {
      case "derrota":
        echo "<h1> GAME OVER </h1>";
        $_SESSION['partida'][0]->setTurno('volverinicio');
        break;
      case 0:
        echo "<h1> Presiona acción para adentrarte en la mazmorra </h1>";
        break;
      case 1:
        if (!isset($_SESSION['goblin'])) {
          $goblin = new Goblin('Goblin Novato', 20, 5, 'imx/globin1.png');
          $datos[] = $goblin;
          $_SESSION['goblin'] = $datos;
        }
        echo "<h3> Combate1 </h3><img src='" . $_SESSION['goblin'][0]->getImagen() . "' > <h2> " . $_SESSION['goblin'][0]->getNombreGoblin() . " </h2>";
        break;
      case 2:
        echo "<h1> Has vencido al Goblin Novato </h1>";
        break;
      case 3:
        $_SESSION['partida'][0]->evento();
        $_SESSION['partida'][0]->ejecutarEvento($_SESSION['partida'][0]->getEvento());        
        break;
        case 4:
          if (!isset($_SESSION['goblin'])) {
            $goblin = new Goblin('Goblin Oficial', 40, 10, 'imx/globin2.png');
            $datos[] = $goblin;
            $_SESSION['goblin'] = $datos;
          }
          echo "<h3> Combate2 </h3><img src='" . $_SESSION['goblin'][0]->getImagen() . "' > <h2> " . $_SESSION['goblin'][0]->getNombreGoblin() . " </h2>";
          break;
        case 5:
          echo "<h1> Has vencido al Goblin Oficial </h1>";
          break;
        case 6:
          $_SESSION['partida'][0]->evento();
          $_SESSION['partida'][0]->ejecutarEvento($_SESSION['partida'][0]->getEvento());          break;
          case 7:
            if (!isset($_SESSION['goblin'])) {
              $goblin = new Goblin('Goblin T-Pose', 60, 20, 'imx/globin3.png');
              $datos[] = $goblin;
              $_SESSION['goblin'] = $datos;
            }
            echo "<h3> Combate3 </h3><img src='" . $_SESSION['goblin'][0]->getImagen() . "' > <h2> " . $_SESSION['goblin'][0]->getNombreGoblin() . " </h2>";
            break;
          case 8:
            echo "<h1> Has vencido al Goblin T-Pose </h1>";
            break;
          case 9:
            $_SESSION['partida'][0]->evento();
            $_SESSION['partida'][0]->ejecutarEvento($_SESSION['partida'][0]->getEvento());
            break;
            case 10:
              if (!isset($_SESSION['goblin'])) {
                $goblin = new Goblin('Goblin Capitán', 100, 30, 'imx/globin4.png');
                $datos[] = $goblin;
                $_SESSION['goblin'] = $datos;
              }
              echo "<h3> Combate4 </h3><img src='" . $_SESSION['goblin'][0]->getImagen() . "' > <h2> " . $_SESSION['goblin'][0]->getNombreGoblin() . " </h2>";
              break;
            case 11:
              echo "<h1> Has vencido al Goblin Capitán </h1>";
              break;
            case 12:
              $_SESSION['partida'][0]->evento();
              $_SESSION['partida'][0]->ejecutarEvento($_SESSION['partida'][0]->getEvento());
              break;
              case 13:
                if (!isset($_SESSION['goblin'])) {
                  $goblin = new Goblin('Goblin Supremo', 150, 40, 'imx/globin5.png');
                  $datos[] = $goblin;
                  $_SESSION['goblin'] = $datos;
                }
                echo "<h3> Combate Final </h3><img src='" . $_SESSION['goblin'][0]->getImagen() . "' > <h2> " . $_SESSION['goblin'][0]->getNombreGoblin() . " </h2>";
                break;
              case 14:
                echo "<h1> Has vencido al Goblin Supremo ¡HAS GANADO! </h1>";
                break;

              

    }
  }






  public function avanzarTurno()
  {
    $this->turno++;
  }

  public function getTurno()
  {
    return $this->turno;
  }

  public function setTurno($turno)
  {
    $this->turno = $turno;
  }

  public function getEvento()
  {
    return $this->evento;
  }

  public function setEvento($evento)
  {
    $this->evento = $evento;
  }

  public function golpe($ataque, $vidaGoblin)   //Se le resta la vida al goblin por el ataque del personaje
  {
    $vidaGoblin -= $ataque;
    $_SESSION['goblin'][0]->setVidaGoblin($vidaGoblin);
  }

  public function esquiva($ataqueGoblin, $esquiva, $vida, $defensa)   //Con esto se calcula el daño que recibe el personaje del jugador en cada ataque, usando la esquiva como un porcentaje para saber si esquivó el daño
  {
    $tiro = random_int(1, 100);
    if ($tiro > $esquiva) {
      $vida -= max(0, $ataqueGoblin - $defensa / 8);
      $_SESSION['xogador'][0]->setVida($vida);
    } else {
      echo "¡Esquivaste el golpe!";
    }
  }

  public function victoria()      //Se ejecuta cuando el jugador presiona el botón en una pantalla de victoria tras vencer a un goblin
  {
    $_SESSION['partida'][0]->avanzarTurno();
    unset($_SESSION['goblin']);
  }

  public function reset()             //Se ejecuta cuando la vida del personaje ha llegado a 0 y el jugador presiona el botón en la pantalla de derrota, devolviéndolo a la pantalla de creación de personaje
  {                                     //También se usa cuando el jugador gana la partida porque realmente se necesita que se haga lo mismo
    unset($_SESSION['partida']);
    unset($_SESSION['xogador']);
    unset($_SESSION['goblin']);
  }

  public function evento()    //Se escoge aleatoriamente de entre 3 eventos. Con la fuente se gana vida, con la colleja se resta y con el cofre se consigue un objeto.
  {
    $evento = rand(0, 2);
    switch ($evento) {
      case 0:
        $_SESSION['partida'][0]->setEvento("Fuente");
        break;
      case 1:
        $_SESSION['partida'][0]->setEvento("Cofre");
        break;
      case 2:
        $_SESSION['partida'][0]->setEvento("Colleja");
        break;
    }
  }

  public function ejecutarEvento($evento)     //se ejecuta uno de los eventos escogidos con el anterior método.
  {
    switch ($evento) {

      case "Fuente":
        $curar = rand(20, 80);
        $masvida = $curar + $_SESSION['xogador'][0]->getVida();
        $_SESSION['xogador'][0]->setVida($masvida);
        echo "<img src='imx/curar.png' > <h2>Te has encontrado con una fuente de curación </h2> <h3>Consigues " . $curar . " puntos de vida </h3> ";       
        break;

      case "Cofre":
        $objeto = rand(0, 5);
        switch ($objeto) {
          case 0:
            $item = "Espada";
            $Sumarataque = $_SESSION['xogador'][0]->getAtaque() + 30;
            $inventario = array();
            $inventario[] = $_SESSION['xogador'][0]->conseguirObjeto($_SESSION['xogador'][0]->getInventario(), $item);
            $_SESSION['xogador'][0]->setAtaque($Sumarataque);
            echo "<img src='imx/espada.png' > <h2>En un cofre has encontrado una espada </h2> <h3>Consigues 30 puntos de ataque </h3> ";
            break;
          case 1:
            $item = "Escudo";
            $Sumarataque = $_SESSION['xogador'][0]->getDefensa() + 30;
            $inventario = array();
            $inventario[] = $_SESSION['xogador'][0]->conseguirObjeto($_SESSION['xogador'][0]->getInventario(), $item);
            $_SESSION['xogador'][0]->setDefensa($Sumarataque);
            echo "<img src='imx/escudo.png' > <h2>En un cofre has encontrado un escudo </h2> <h3>Consigues 30 puntos de defensa </h3> ";
            break;
          case 2:
            $item = "Armadura";
            $Sumarataque = $_SESSION['xogador'][0]->getVida() + 30;
            $inventario = array();
            $inventario[] = $_SESSION['xogador'][0]->conseguirObjeto($_SESSION['xogador'][0]->getInventario(), $item);
            $_SESSION['xogador'][0]->setVida($Sumarataque);
            echo "<img src='imx/armadura.png' > <h2>En un cofre has encontrado una armadura </h2> <h3>Consigues 30 puntos de vida </h3> ";
            break;
          case 3:
            $item = "Capa";
            $Sumarataque = min(100, $_SESSION['xogador'][0]->getEsquiva() + 30);
            $inventario = array();
            $inventario[] = $_SESSION['xogador'][0]->conseguirObjeto($_SESSION['xogador'][0]->getInventario(), $item);
            $_SESSION['xogador'][0]->setEsquiva($Sumarataque);
            echo "<img src='imx/capa.png' > <h2>En un cofre has encontrado una capa </h2> <h3>Consigues 30 puntos de esquiva </h3> ";
            break;
          case 4:
            $item = "Espada+";
            $Sumarataque = $_SESSION['xogador'][0]->getAtaque() + 60;
            $inventario = array();
            $inventario[] = $_SESSION['xogador'][0]->conseguirObjeto($_SESSION['xogador'][0]->getInventario(), $item);
            $_SESSION['xogador'][0]->setAtaque($Sumarataque);
            echo "<img src='imx/espada+.png' > <h2>En un cofre has encontrado la espada de las edades </h2> <h3>Consigues 60 puntos de ataque </h3> ";
            break;
          case 5:
            $item = "Armadura+";
            $Sumarataque = $_SESSION['xogador'][0]->getVida() + 60;  
            $inventario = array();         
            $inventario[] = $_SESSION['xogador'][0]->conseguirObjeto($_SESSION['xogador'][0]->getInventario(), $item);
            $_SESSION['xogador'][0]->setVida($Sumarataque);
            echo "<img src='imx/armadura+.png' > <h2>En un cofre has encontrado la armadura de las edades </h2> <h3>Consigues 60 puntos de vida </h3> ";
            break;
        }
        break;
        case "Colleja":
          $daño = rand(0,10);
          $vidarestante = $_SESSION['xogador'][0]->getVida() - $daño;
          $_SESSION['xogador'][0]->setVida($vidarestante);
          echo "<img src='imx/globin1.png' > <h3>El Goblin Novato ha vuelto para pegarte, pierdes " . $daño . " puntos de vida </h3> ";

        break;
    }
  }
}
