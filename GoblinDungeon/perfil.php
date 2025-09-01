<?php
/*
  Autor: Francisco Javier Muiños López
  Fecha de Última modificación: 21/11/2022
  Versión: 0.91989928982995
 */
session_start();
include("Modelos/DAO.class.php");
?>
<html>

<head>
<link rel="stylesheet" href="estilos/maquetacio.css">
    <?php
    $fechas = [];
    $error = "";
    $archivo = "csv/registro.csv";
    $datos = Dao::leerPersonaje($archivo);

    if (isset($_POST['modificar'])) {
        if(empty($_POST['estilo']) or empty($_POST['fuente']) or empty($_POST['fuenteest'])) {
            $error = "Debes cubrir todos los campos";
        } else{
        $estilo = $_POST["estilo"];         //Si cambio algo sobre como funciona el recargar la página para cargar los estilos, el mensaje de número de visitas falla
        setcookie("estilo", $estilo, time() + (60 * 60 * 24 * 90));
        $fuente = $_POST["fuente"];
        setcookie("fuente", $fuente, time() + (60 * 60 * 24 * 90));
        $fuenteest = $_POST["fuenteest"];
        setcookie("fuenteest", $fuenteest, time() + (60 * 60 * 24 * 90));

        echo '<link rel="stylesheet" href="estilos/' . $_POST['estilo'] . '.css">';
        
            echo '<style> body { font-size:' . $_POST['fuente'] . 'px; }
    .form1 { font-size:' . $_POST['fuente'] . 'px; } 
    .form2 { font-size:' . $_POST['fuente'] . 'px; }            
    .submit { font-size:' . $_POST['fuente'] . 'px; }  
    .submit:active { font-size:' . $_POST['fuente'] . 'px;
        .menu { float:right; } }           </style>';
        
              
            echo '<style> body { font-size:' . $_POST['fuenteest'] . 'px; }
    .form1 { font-family:' . $_POST['fuenteest'] . 'px; } 
    .form2 { font-family:' . $_POST['fuenteest'] . 'px; }
    .submit { font-family:' . $_POST['fuenteest'] . 'px; }  
    .submit:active { font-family:' . $_POST['fuenteest'] . 'px; } 
    .error { color:red; }    </style>';
        } }
    else if (isset($_POST['volver'])) {
        setcookie("estilo", "0", time() - 1);
        setcookie("fuente", "0", time() - 1);       //se asignan en cookies los valores establecidos en el form
        setcookie("fuenteest", "0", time() - 1);    //cambiando los estilos de css, fuente o tamaño de la letra

        echo '<link rel="stylesheet" href="estilos/maquetacio.css">';
        
            echo '<style> body { font-size:20px; }
        .form1 { font-size:20px; } 
        .form2 { font-size:20px; }
        .submit { font-size:20px; }  
        .submit:active { font-size:20px;
            .menu { float:right; } }           </style>';              
            echo '<style> body { font-size:20px; }
        .form1 { font-family:arial; } 
        .form2 { font-family:arial; }
        .submit { font-family:arial; }  
        .submit:active { font-family:arial; } 
        .menu { float:right; } 
           </style>';
    }
    
    else if (isset($_COOKIE['estilo'])) {
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


    if (isset($_POST['subirimx'])) {

        global $fichero;        //si se añade una imagen comienza el proceso de crear el avatar del usuario

        //Recogemos el fichero enviado por el formulario

        $fichero = $_FILES['image']['name'];

        //Si el fichero contiene algo y es diferente de vacio

        if (isset($fichero) && $fichero != "") {

            //Obtenemos algunos datos necesarios sobre el fichero

            $tipo = $_FILES['image']['type'];

            $tamano = $_FILES['image']['size'];

            $temp = $_FILES['image']['tmp_name'];

            //Se comprueba si el fichero a cargar es correcto observando su extensión y tamaño

            if (!(strpos($tipo, "png")) && ($tamano < 2000000)) {
                echo '<div><b> Error. La extensión o el tamaño de los archivos no es correcta.<br> - Se permiten archivos .png y de 200 kb como máximo.</b></div>';
            } else {

                //Si la imagen es correcta en tamaño y tipo (solo acepta .png)

                // Se intenta subir al servidor

                if (move_uploaded_file($temp, './imx/' . $_SESSION['usuario'] . ".png")) {

                    //Cambiamos los permisos del fichero a 777 para poder modificarlo posteriormente

                    chmod('./imx/' . $_SESSION['usuario'] . ".png", 0777);
                } else {

                    // Si no se ha podido subir la imagen, mostramos un mensaje de error

                    echo '<div><b>Ocurrió algún error al subir el fichero. No pudo guardarse.</b></div>';
                }
            }
        }
    }


    ?>
    <meta charset="utf-8">
</head>

<body>
    <?php include 'menu.php'; ?>
    <div class="form1">
        <fieldset>
            <legend>
                <font color="#6042B9">Foto de Perfil</font>
            </legend>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
                <?php
                if (file_exists('imx/' . $_SESSION['usuario'] . ".png")) {
                    echo "<img src=imx/" . $_SESSION['usuario'] . ".png>";
                } else {
                    echo "<img src=imx/default.jpg>";
                }



                ?>
                <span id="subirimx">
                    Seleccionar imaxe ( solo .png)
                    <input accept="image/png" type="file" value="Examinar" name="image"></input></span>
                <br><span id="subirimx"><button name="subirimx" type="submit">Upload</button></span>
            </form>
        </fieldset>
    </div>
    <div class="form2">
        <fieldset>
            <legend>
                <font color="#6042B9">Preferencias</font>
            </legend>
            <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <br>
                <select id="estilo" name="estilo">
                    <optgroup label="Estilos">
                        <option selected value="0"> (Seleccionar Estilo) </option>
                        <option value="gatico">gatico</option>
                        <option value="pescao">pescao</option>
                    </optgroup>
                </select>
                <br> <br>
                <p>
                    Tamaño de la fuente: <input type="number" name="fuente" min="12" max="48"> </input> <br>

                </p>
                <br>
                <select id="fuente" name="fuenteest">
                    <optgroup label="Fuente">
                        <option selected value="0"> (Seleccionar Fuente) </option>
                        <option value="Helvetica">Helvetica</option>
                        <option value="sans-serif">sans-serif</option>
                    </optgroup>
                </select>
                <p>
                    <input type="submit" value="Modificar" name="modificar"></input>
                    <input type="submit" value="Valores por defecto" name="volver"></input> &nbsp <span class="error">* <?php echo $error; ?> </span>
                </p>
            </form>
        </fieldset>
    </div>
    <br>

    <p>
        <table border="1" aria-describedby="Personajes Ganadores">
            <caption>Personajes Ganadores</caption>
            <tr>
                <th>Nombre</th>
                <th>Clase</th>
                <th>Vida</th>
                <th>Ataque</th> 
                <th>Defensa</th>   
                <th>Esquiva</th>   
                <th>Inventario</th>                
            </tr>
            <?php
            $contFila = 0;
            foreach ($datos as $personaje) {
            ?>
                <tr>
            <?php
                              
                echo "<th>".$personaje->getNombre()."</th>";
                echo "<th>".$personaje->getClase()."</th>";
                echo "<th>".$personaje->getVida()."</th>";   
                echo "<th>".$personaje->getAtaque()."</th>";  
                echo "<th>".$personaje->getDefensa()."</th>";  
                echo "<th>".$personaje->getEsquiva()."</th>";  
                echo "<th>".$personaje->getInventario()."</th>";     
                
            }
         
            ?>
        </table>
        <p></p>
    
    <table border="1" aria-describedby="tabla usuarios">
        <caption>Control de accesos</caption>
        <?php

        if (isset($_COOKIE['fecha'])) {
            $contador = 0;
            $accesos = explode(",", $_COOKIE['fecha']);
            foreach ($accesos as $valor) {
                $contador++;
                echo "<tr> <th>" . $contador . "</th> <th>" . $valor . "</th> </tr>";
            }
        }

        $fechas_acceso = explode(",", $_COOKIE['fecha']);
        $numfec = count($fechas_acceso);
        if ($numfec >= 2) {
            $ultimo = ($numfec) - 1;
            $penultimo = ($numfec) - 2;
            $ultimafecha = $fechas_acceso[$ultimo];
            $penultimafecha = $fechas_acceso[$penultimo];
            $v1 = strtotime($penultimafecha);
            $v2 = strtotime($ultimafecha);
            $tiemposinacc = $v2 - $v1;
            if ($tiemposinacc >= 3600) {          //si lleva más de 1 hora sin acceder se avisa al usuario
                echo "Llevas mucho sin acceder pillín";
            }
        }
        ?>

</body>

</html>