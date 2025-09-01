<?php
/*
  Autor: Francisco Javier Muiños López
  Fecha de Última modificación: 14/11/2022
  Versión: 0.9
 */
session_start();
?>

<html>

<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="estilos/maquetacio.css">
    <style>
        .error {
            color: red;
        }

        caption {
            color: black;
        }

        table {
            background-color: grey;
            color: yellow;
        }
    </style>

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
    <?php include_once('menu.php'); ?>
    <?php
    include('Modelos/DAO.class.php');
    if ($_SESSION['rol'] == 'administrador') { //si el usuario no es administrador, se le redirige a login.php


        function test_input($data)
        {
            return htmlspecialchars(stripslashes(trim($data)));
        }

        if (isset($_POST['logoff'])) {   // si se presiona el botón de logoff, se hace unset a la sesión
            session_unset();
            header('Location: login.php');
        }
        
        $rolErr = $nombreErr = $contrasenaErr = $contrasena2Err = $direccionErr = $emailErr = $telefonoErr = $fechaErr = $ciudadErr = $comicsErr = $peliculasErr = $animuErr = $textoErr = ''; //inicializamos las variables del formulario con sus mensajes de error
        $rol = $nombre = $contrasena = $contrasena2 = $direccion = $email = $telefono = $fecha = $ciudad = $comics = $peliculas = $animu = $texto = '';
        $_SESSION['token-csrf'] = base64_encode(openssl_random_pseudo_bytes(48)); //generamos el token
        $archivo = 'csv/autenticacion2.csv';
        $Erros = [];
        $contrasenahash = crypt($contrasena, '$6$rounds=5000$usesomesillystringforsalt$');
        $datos = Dao::obtenerUsuarios($archivo);




        if (isset($_POST['enviar'])) {

            if (empty($_POST['rol'])) {
                $Erros[] = 'Tienes que poner el rol';        //se genera un mensaje de error para cada campo obligatorio si no es cubierto
                $rolErr = 'Tienes que poner el rol';
            } else {
                $rol = test_input($_POST['rol']);
            }

            if (empty($_POST['nombre'])) {
                $Erros[] = 'Tienes que poner el nombre';        
                $nombreErr = 'Tienes que poner el nombre';
            } else {
                validarNombre($nombre);
                $nombre = test_input($_POST['nombre']);
            }

            if (empty($_POST['contrasena'])) {
                $Erros[] = 'Debes poner la contraseña';
                $contrasenaErr = 'Debes poner la contraseña';
            } elseif ($_POST['contrasena2'] != $_POST['contrasena']) {
                $Erros[] = 'Debes poner la contraseña';
                $contrasenaErr = 'Las contraseñas no coindicen';
            } else {
                validarC($contrasena);
                $contrasena = test_input($_POST['contrasena']);
            }

            if (empty($_POST['contrasena2'])) {
                $Erros[] = 'Debes poner la contraseña';
                $contrasena2Err = 'Debes poner la contraseña';
            } elseif ($_POST['contrasena2'] != $_POST['contrasena']) {
                $Erros[] = 'Debes poner la contraseña';
                $contrasena2Err = 'Las contraseñas no coindicen';
            } else {
                validarCREP($contrasena2);
                $contrasena2 = test_input($_POST['contrasena']);
            }

            if (empty($_POST['email'])) {
                $Erros[] = 'Tienes que poner el email';
                $emailErr = 'Tienes que poner el email';
            } else {
                validarEmail($email);
                $email = test_input($_POST['email']);
            }

            if (empty($_POST['fecha'])) {
                $Erros[] = 'Tienes que poner la fecha';
                $fechaErr = 'Tienes que poner la fecha';
            } else {
                validarFecha($fecha);
                $fecha = test_input($_POST['fecha']);
            }

            
            $token = filter_input(INPUT_POST, 'token-csrf', FILTER_SANITIZE_STRING);
            validarEmail($email);

            validarNombre($nombre);

            validarC($contrasena);

            validarCRep($contrasena2);
           

            if (count($Erros) == 0) {
                //si no hay errores, se mete en el array datos los valores introducidos en el formmulario
                $usuario = new Usuario($rol, $nombre, crypt($contrasena, '$6$rounds=5000$usesomesillystringforsalt$'), $email, $fecha);
                $datos[] = $usuario;
                Dao::escribirUsuario($archivo, $datos);
            }
        }





    ?>

        <div class="form1">

            <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <fieldset>

                    <font color="#6042B9">Novo usuario</font>

                </fieldset>
                <br>

                <fieldset>
                    <legend> Datos Personales </legend>

                    <select id="rol" name="rol">
                        <optgroup label="Rol">
                            <option selected value="0"> (Seleccionar rol) </option>
                            <option value="usuario">usuario</option>
                            <option value="administrador">administrador</option>
                        </optgroup>
                    </select> <span class="error">* <?php echo $rolErr; ?></span> </p>
                    <br>
                    <p>
                        <label for="nombre"> Usuario </label> <input tabindex='1' type="text" name="nombre" id="nombre" label="nombre" size="30" value="<?php if (isset($_POST['enviar'])) {
                                                                                                                                                            echo $_POST['nombre'];
                                                                                                                                                        } ?>"> </input> &nbsp <span class="error">* <?php echo $nombreErr; ?> </span>
                    </p>
                    <p>
                        <label for="contrasena">Contraseña</label> <input tabindex='2' type="password" name="contrasena" id="contrasena" label="contraseña" size="30" value="<?php if (isset($_POST['enviar'])) {
                                                                                                                                                                                    echo $_POST['contrasena'];
                                                                                                                                                                                } ?>"> </input> <span class="error">* <?php echo $contrasenaErr; ?></span>
                    </p>
                    </p>
                    <p>
                        <label for="contrasena2">Confirmar contraseña</label> <input tabindex='3' type="password" name="contrasena2" id="contrasena2" label="confirmarpass" size="30" value="<?php if (isset($_POST['enviar'])) {
                                                                                                                                                                                                    echo $_POST['contrasena2'];
                                                                                                                                                                                                } ?>"> </input> <span class="error">* <?php echo $contrasena2Err; ?></span>
                    </p>
                    </p>
                    
                    <p>
                        <label for="email">Email</label> <input tabindex='5' type="text" name="email" id="email" label="email" size="30" value="<?php if (isset($_POST['enviar'])) {
                                                                                                                                                    echo $_POST['email'];
                                                                                                                                                } ?>"> </input> &nbsp <span class="error">* <?php echo $emailErr; ?> </span>
                    </p>
                    <input type="hidden" name="token-csrf" value="<?php echo $_SESSION['token'] ?? '' ?>">
                   
                    <p>
                        <label for="fecha">Fecha de nacimiento:</label> <input tabindex='7' type="date" id="fecha" name="fecha" label="fecha" value="<?php if (isset($_POST['enviar'])) {
                                                                                                                                                            echo $_POST['fecha'];
                                                                                                                                                        } ?>" min="1950-01-01" max="2022-10-17">
                    </p> &nbsp <span class="error">* <?php echo $fechaErr; ?> </span>
                </fieldset>
        </div>
        

                <p>
                    <span class="error"> Todos los campos son obligatorios </span>
                </p>


                <input tabindex='17' class="submit" type="submit" value="Rexistrar" name="enviar"></input>
                <input tabindex='18' class="submit" type="reset" value="Borrar" name="reset"></input>


                </p>
            </fieldset>
            </form>
        </div>
        <p>
        <table border="1" aria-describedby="tabla usuarios">
            <caption>Datos</caption>
            <tr>
                <th>Rol</th>
                <th>Nombre</th>
                <th>Contraseña</th>
                <th></th>              
            </tr>
            <?php
            $contFila = 0;
            foreach ($datos as $usuario) {
            ?>
                <tr>
            <?php
                //dibujamos la tabla con el valor en cada <th> ordenado debajo de su nombre, seguido del enlace para eliminarla.              
                echo "<th>".$usuario->getRol()."</th>";
                echo "<th>".$usuario->getNombre()."</th>";
                echo "<th>".$usuario->getContrasena()."</th>";      
                echo "<th> <a href='borrar.php?id=" . $contFila++ . "'>Eliminar</a></th> </tr>";
            }
        } else {
            header('Location: login.php');
        }
            ?>
        </table>
        <p></p>
        <p></p>


</body>

</html>