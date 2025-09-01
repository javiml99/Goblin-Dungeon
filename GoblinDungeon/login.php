<?php
/*
  Autor: Francisco Javier Muiños López
  Fecha de Última modificación: 14/11/2022
  Versión: 0.2
 */
session_start();
?>
<html>

<head>
    <link rel="stylesheet" href="estilos/maquetacio.css">
    <meta charset="utf-8">
    <?php
    if (isset($_COOKIE['estilo'])) {
        echo '<link rel="stylesheet" href="estilos/' . $_COOKIE['estilo'] . '.css">';
    }
    else if (isset($_COOKIE['fuente'])) {
        echo '<style> body { font-size:' . $_COOKIE['fuente'] . 'px; }</style>';
    }
    else if (isset($_COOKIE['fuenteest'])) {
        echo '<style> body { font-family:' . $_COOKIE['fuenteest'] . 'px; }</style>';
    }
    else {
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
    <?php
    include_once('menu.php');
    include_once('funciones.php');
    //inicializamos la variable con el nombre del archivo csv y el array que contendrá los datos
    $archivo = 'csv/autenticacion2.csv';
    $usuarioEncontrado = false;

    $datos = lerCSV($archivo);
    $nombre = $contrasena = '';

    if (isset($_POST['enviar'])) {
        if (empty($_POST['nombre'])) {
            $Erros[] = 'Tienes que poner el nombre';
            $nombreErr = 'Tienes que poner el nombre';
        } else {
            $nombre = test_input($_POST['nombre']);
        }

        if (empty($_POST['contrasena'])) {
            $Erros[] = 'Debes poner la contraseña';
            $contrasenaErr = 'Debes poner la contraseña';
        } else {
            $contrasena = test_input($_POST['contrasena']);
        }

        if (isset($Erros) && count($Erros) == 0) {
            //si no hay errores, se mete en el array datos los valores introducidos en el formulario
            $arrayInsertar[] = $nombre;
            $arrayInsertar[] = $contrasena;
            $datos[] = $arrayInsertar;
        }
    }


    if (isset($_COOKIE['estilo'])) {
        echo '<link rel="stylesheet" href="estilos/' . $_COOKIE['estilo'] . '.css">';
    }
    if (isset($_COOKIE['fuente'])) {
        echo '<style> body { font-size:' . $_COOKIE['fuente'] . 'px; }</style>';
    }

    function test_input($data)
    {
        return htmlspecialchars(stripslashes(trim($data)));
    }

    if (isset($_POST['enviar'])) {
        if (empty($_POST['nombre']) || empty($_POST['contrasena'])) {    //si el nombre y contraseña esta vacio saldra este mensaje de error
            echo "<div style='text-align: center;'> Debe introducir el Usuario y contraseña </div>";
        } else {
            $i = 0;
            $contrasenaCifrada = crypt($_POST['contrasena'], '$6$rounds=5000$usesomesillystringforsalt$');
            while (!$usuarioEncontrado && $i < count($datos)) {
                if ($_POST['nombre'] == $datos[$i][1]) {
                    if (
                        hash_equals(                // si el nombre de usuario y la contraseña son los mismos que en los del csv...
                            $datos[$i][2],
                            crypt($_POST['contrasena'], $datos[$i][2])
                        )
                    ) {
                        if (!isset($_COOKIE['fecha'])) {                          
                            $fechas = array();
                            $fechas[] = date('d-m-o G:i:s');
                            $stringfechas = implode(",", $fechas);
                            setcookie('fecha', $stringfechas, time() + 3600 * 24);
                        }
                     else if (isset($_COOKIE['fecha'])) {                      
                        setcookie('fecha', $_COOKIE['fecha'], time() + 3600 * 24);
                        $fecha_actual = date('d-m-o G:i:s');
                        $fechas[] = $_COOKIE['fecha'];
                        array_push($fechas, $fecha_actual);
                        $stringfechas = implode(',', $fechas);
                        setcookie('fecha', $stringfechas, time() + 3600 * 24);
                    }
                        $usuarioEncontrado = true;
                        $_SESSION['rol'] = $datos[$i][0];        //... se accederá a la página usuarios.php
                        $_SESSION['usuario'] = $datos[$i][1];
                        header('Location: perfil.php');
                        exit();
                    }
                }
                $i++;
            }
            if ($usuarioEncontrado == false) {
                echo "<div style='text-align: center;'> Usuario o Contraseña Inválido </div>";
            }
        }
    }
    ?>
    <p></p>
    <p></p>
    <div class="form1">
    <fieldset>
        <legend>
            <font color="#6042B9">Acceso</font>
        </legend>
        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <p>
                Usuario <input type="text" name="nombre" size="30"> </input> <span class="error">* <?php echo isset($nombreErr) ? $nombreErr : ''; ?></span>
            </p>
            <p>
                Contraseña <input type="password" name="contrasena" size="30"> </input> <span class="error">* <?php echo isset($contrasenaErr) ? $contrasenaErr : ''; ?></span>
            </p>
            <p>
                <input type="submit" value="Entrar" name="enviar"></input>
            </p>

            <br>
            <p> Administrador: javi contraseña: sss </p>
            <br>
            <p> Usuario: usuario contraseña: sss </p>
    </fieldset>
    </form>
</div>

</body>

</html>