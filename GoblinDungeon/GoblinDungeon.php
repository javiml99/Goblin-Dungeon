<?php
/*
  Autor: Francisco Javier Muiños López
  Fecha de Última modificación: 23/11/2022
  Versión: 0.2
 */
include("Modelos/Partida.class.php");
include("Modelos/DAO.class.php");
session_start();
?>
<html>

<head>
  <link rel="stylesheet" href="estilos/maquetacio.css">
  <meta charset="utf-8">
  <?php
  /* if (isset($_SESSION['xogador'])) {
    echo "<pre>" . var_dump($_SESSION['xogador']) . "</pre>";
  }

  if (isset($_SESSION['partida'])) {
    echo  "<pre>" . var_dump($_SESSION['partida']) . "</pre>";
  } */
  if (isset($_COOKIE['estilo'])) {
    echo '<link rel="stylesheet" href="estilos/' . $_COOKIE['estilo'] . '.css">';
  } else if (isset($_COOKIE['fuente'])) {
    echo '<style> body { font-size:' . $_COOKIE['fuente'] . 'px; }</style>';
  } else if (isset($_COOKIE['fuenteest'])) {
    echo '<style> body { font-family:' . $_COOKIE['fuenteest'] . 'px; }</style>';
  } else {
    echo '<style>.form1 {
            background-color: #E6B99C;
              border-radius: 20px;
              box-sizing: border-box;
            color: #A88AEB;
              font-family: sans-serif;
              font-size: 36px;
              font-weight: 600;
              margin-top: 30px;
            
            }
            #menu {
              float:right; 
            }
            
            #subirimx {
              float:center; 
            }
            
            .error {
              color: red;
            }
            
            img{
              max-width: 150px;
              height: auto;
            }
            
            .form2 {
            background-color: #E6B99C;
              border-radius: 20px;
              box-sizing: border-box;
            color: #A88AEB;
              font-family: sans-serif;
              font-size: 36px;
              font-weight: 600;
              margin-top: 30px;
            
            }
            
            .submit {
              background-color: #08d;
              border-radius: 12px;
              border: 0;
              box-sizing: border-box;
              color: #eee;
              cursor: pointer;
              font-size: 18px;
              
            }
            
            .submit:active {
              background-color: #06b;
            }</style>';
  }
  ?>
</head>

<body>
  <?php include 'menu.php'; ?>
  <?php
  $clase = $personaje = $nombreErr =  '';
  $Erros = [];
  $archivo = "csv/registro.csv";
  $datospersonaje = Dao::leerPersonaje($archivo);;


  if (!isset($_SESSION['xogador']) && isset($_SESSION['usuario'])) {



    if (isset($_POST['enviar'])) {


      if (empty($_POST['nombre'])) {
        $Erros[] = 'Tienes que poner el nombre';
        $nombrejeErr = 'Tienes que poner el nombre';
      } else {
        $nombre = $_POST['nombre'];
      }

      if (empty($_POST['clase'])) {
        $Erros[] = 'Tienes que poner la clase';
      } else {
        $clase = $_POST['clase'];
      }


      if (count($Erros) == 0) {
        switch ($clase) {
          case 'guerrero':
            $vida = 50;
            $clase = "Guerrero";
            $ataque = 50;
            $defensa = 30;
            $esquiva = 5;
            $inventario = array();
            break;

          case 'paladin':
            $vida = 100;
            $clase = "Paladín";
            $ataque = 30;
            $defensa = 50;
            $esquiva = 2;
            $inventario = array();
            break;

          case 'picaro':
            $vida = 40;
            $clase = "Pícaro";
            $ataque = 80;
            $defensa = 15;
            $esquiva = 25;
            $inventario = array();
            break;
        }
        $personaje = new Xogador($nombre, $clase, $vida, $ataque, $defensa, $esquiva, $inventario);
        $datos[] = $personaje;
        $_SESSION['xogador'] = $datos;
      }
    }
  }
  if (isset($_SESSION['partida'])) {
    $partida = $_SESSION['partida'][0];
    if ($_SESSION['xogador'][0]->getVida() <= 0) {
      $_SESSION['partida'][0]->setTurno("derrota");
      //Si durante la partida, la vida del personaje llega a 0, se devuelve a la pantalla de creación de personaje, dándole el valor a la variable turno de "derrota"
      if (isset($_POST['volver'])) {
        $partida->reset();
      }
    } else {
      if(!isset($_SESSION['goblin'])){

      if (isset($_POST['accion']) && $partida->getTurno() == 0) {       //Aquí se controla lo que pasa cuando el usuario le da al botón acción cuando no está en combate
        $partida->avanzarTurno();
      }
      //primer combate
      else if (isset($_POST['accion']) && $partida->getTurno() == 2) {
        $partida->avanzarTurno();
      }

      else if (isset($_POST['accion']) && $partida->getTurno() == 3) {
        $partida->avanzarTurno();
      }
      //segundo combate
      else if (isset($_POST['accion']) && $partida->getTurno() == 5) {
        $partida->avanzarTurno();
      }
      else if (isset($_POST['accion']) && $partida->getTurno() == 6) {
        $partida->avanzarTurno();
      }
      //tercer combate
      else if (isset($_POST['accion']) && $partida->getTurno() == 8) {
        $partida->avanzarTurno();
      }
      else if (isset($_POST['accion']) && $partida->getTurno() == 9) {
        $partida->avanzarTurno();
      }
      //cuarto combate
      else if (isset($_POST['accion']) && $partida->getTurno() == 11) {
        $partida->avanzarTurno();
      }
      else if (isset($_POST['accion']) && $partida->getTurno() == 12) {
        $partida->avanzarTurno();
      }
      //quinto combate
      else if (isset($_POST['accion']) && $partida->getTurno() == 14) {
        $datospersonaje[] = $_SESSION['xogador'][0];
        Dao::escribirPersonaje($archivo, $datospersonaje);
        $partida->reset();
      }
    }

      if(isset($_SESSION['goblin'])){
      if ($_SESSION['goblin'][0]->getVidaGoblin() <= 0) {
        $partida->victoria();
      } else if (isset($_POST['accion'])) {
        $partida->golpe($_SESSION['xogador'][0]->getAtaque(), $_SESSION['goblin'][0]->getVidaGoblin());
        $partida->esquiva($_SESSION['goblin'][0]->getAtaqueGoblin(), $_SESSION['xogador'][0]->getEsquiva(), $_SESSION['xogador'][0]->getVida(), $_SESSION['xogador'][0]->getDefensa());
        //echo  "<pre>" . var_dump($_SESSION['goblin']) . "</pre>";
      }
    }
  }
  }


  ?>

  <div id="contPrincipal">
    <div id="contMenu">
      <br><br><br>
      <?php
      if (!isset($_SESSION['usuario'])) {
        echo "<h1> Debes iniciar sesión para jugar </h1> <h2> Crea una cuenta o inicia sesión en una ya existente</h2>";
      }
      if ((!isset($_SESSION['xogador']) && isset($_SESSION['usuario']))) {
        echo "
        <form method='post' action='" . $_SERVER['PHP_SELF'] . "'>
          <span id='texto'> Escoge tu clase: </span>
          <select name='clase'>
            <option value='guerrero'> Guerrero </option>
            <option value='paladin'> Paladín</option>
            <option value='picaro'> Pícaro </option>
          </select>  <br>
          <span id='texto'> El guerrero es un personaje estándar, sin nada que destacar </span> <br>
          <span id='texto'> El paladín tiene una alta resistencia y mucha vida, pero carece de ataque </span> <br>
          <span id='texto'> El pícaro es letal pero frágil, depende de su mayor posibilidad de esquivar </span> <br> <br>
          Dale un nombre a tu personaje: <input type='text' name='nombre' id='nombre' label='nombre' size='30'> </input> <span class='error'>* <?php  echo $nombreErr; ?> </span>

          <p> <input class='submit' type='submit' value='Comenzar' name='enviar'></input> </p>
        </form>
        ";
      }

      if (isset($_SESSION['xogador']) && isset($_SESSION['usuario'])) {
        if (!isset($_SESSION['partida'])) {
          $turno = 0;
          $partida = new Partida($turno, "");
          $datospartida[] = $partida;
          $_SESSION['partida'] = $datospartida;
          $partida->controladorTurnos($partida->getTurno());
        } else {
          $partida = $_SESSION['partida'][0];
          $partida->controladorTurnos($partida->getTurno());
        }
      }




      ?>
    </div>
    <div id="contSecundario">
      <div id="contInterfaz">
        <div id="contEstadisticas">
          <?php
          if (isset($_SESSION['xogador']) && isset($_SESSION['usuario'])) {
            $xogador = $_SESSION['xogador'][0];
            echo "<p>Nombre del Personaje: " . $xogador->getNombre() . "</p>";
            echo "<p>Clase: " . $xogador->getClase() . "</p>";
            echo "<p>Vida: " . $xogador->getVida() . "</p>";
            echo "<p>Ataque: " . $xogador->getAtaque() . "</p>";
            echo "<p>Defensa: " . $xogador->getDefensa() . "</p>";
            echo "<p>Esquiva: " . $xogador->getEsquiva() . "</p>";
          }
          
          ?>
        </div>
        <div id="contOpciones">
          <br><br><br><br>
          <?php
          if (isset($_SESSION['xogador']) && isset($_SESSION['usuario'])) {
            if (!isset($_SESSION['goblin'])) {
              echo "<form method='post' action='" . $_SERVER['PHP_SELF'] . "'>
        <input type='submit' class='submit' value='Acción' name='accion'> 

      </form>";
            } elseif (isset($_SESSION['goblin'])) {
              echo "<form method='post' action='" . $_SERVER['PHP_SELF'] . "'>
        <input type='submit' class='submit' value='Atacar' name='accion'> 

      </form>";
            }
            if ($_SESSION['partida'][0]->getTurno() == "volverinicio") {
              echo "<form method='post' action='" . $_SERVER['PHP_SELF'] . "'>
        <input type='submit' class='submit' value='Volver' name='volver'> 

      </form>";
            }
          }


          ?>
        </div>
      </div>
      <div id="contInventario">
        <h3> Inventario </h3> <br>
        <ol>
          <?php
          if (isset($_SESSION['xogador'])) {
            foreach ($_SESSION['xogador'][0]->getInventario() as $objeto) {
              echo "<li> $objeto </li>";
            }
          }
          ?>
        </ol>
      </div>
    </div>

  </div>




  
  <footer> Copyrai Yo (ElJabi) © </footer>
  


</body>

</html>