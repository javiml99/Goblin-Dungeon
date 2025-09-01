<?php
/*
  Autor: Francisco Javier Muiños López
  Fecha de Última modificación: 21/11/2022
  Versión: 0.9
 */
?>
<p>
    <?php
    include_once 'funciones.php';
    if (isset($_SESSION['usuario'])) {
    echo "<span class='usuario'>";
    if (file_exists('imx/' . $_SESSION['usuario'] . ".png")) {
        echo "<a href='perfil.php'> <img height='50px' width='50px' src=imx/" . $_SESSION['usuario'] . ".png> </a> &nbsp" .$_SESSION['usuario'] . "&nbsp &nbsp GOBLIN DUNGEON";
    } else {
        echo "    <a href='provisional.php'> <img src='imx/default.jpg' height='50px' width='50px'> </a> &nbsp &nbsp &nbsp GOBLIN DUNGEON";
    }}
    
    if (!isset($_SESSION['usuario'])){
        echo "    <a href='login.php'> <img src='imx/default.jpg' height='50px' width='50px'> </a> &nbsp &nbsp &nbsp GOBLIN DUNGEON";
    }
       
    ?>
    </span>
    <span id="menu">
        <?php
        if (isset($_SESSION['rol'])) {
            if ($_SESSION['rol'] == 'administrador') {
                echo '<a href="usuarios.php"> Usuarios </a>&nbsp;';
            }
        }
        ?>
        &nbsp;
        <a href="GoblinDungeon.php"> Index </a> &nbsp;
        
        <?php
        if (isset($_SESSION['rol']) && isset($_SESSION['usuario'])) {
            echo "<a href='perfil.php'> Perfil </a> &nbsp;";          
        } else {
            
        }

        if (isset($_SESSION['rol']) && isset($_SESSION['usuario'])) {
            echo "<a href='cerrar.php'> Cerrar sesión </a> &nbsp;";
            echo "<img src='imx/huh.jpg' height='50px' width='50px'> </span>";
        } else {
            echo "<a href='login.php'> Iniciar Sesión </a> &nbsp;";
            echo "<a href='rexistro.php'> Rexistrarse </a> &nbsp;";
            echo "<img src='imx/huh.jpg' height='50px' width='50px'> </span>";
        }
        
        ?>
        
        
        
        



</p>