<?php
/*
 * Copyright 2019 Genereitor
 *
 * Software protegido por la propiedad intelectual.
 * Queda prohibido copiar, modificar, fusionar, publicar, distribuir, sublicenciar y / o vender
 * copias no autorizadas del software
 *
 * El aviso de copyright anterior y este aviso de permiso se incluirán en todas las copias o 
 * porciones sustanciales del software.
 */

/**
 * Description
 *
 * @author Rafael Perez Sanchez
 */
// PASO 1 - si el archivo Conexion ya esta creado, redireccionamos a la lista de tablas
// 
if (file_exists('Conexion/Conexion.php')) {
    header("Location: tableList.php");
}

// PASO 2 - creamos el archivo Conexion y redireccionamos a la lista de tablas
if (isset($_POST['newConexion'])) {
    require_once 'kernel/ConexionCreator.php';
    require_once 'kernel/AutoLoaderCreator.php';
    require_once 'kernel/ConfigCreator.php';
    require_once 'kernel/File.php';
    $hostName = $_POST['hostName'];
    $user = $_POST['user'];
    $password = $_POST['password'];
    $ddbbName = $_POST['bbddName'];
    $conexionCreate = new ConexionCreator($hostName, $user, $password, $ddbbName);
    $conexionCreate->paramCreate();
    $conexionCreate->classCreate();

    $file = new File;

    // CREAMOS EL ARCHIVO DE CONEXION A LA BBDD
    $conexionName = "Conexion.php";
    $conexionDir = "Conexion/";
    //$conexionFullPath = $conexionDestini.$conexionName;    
    $file->create($conexionName, $conexionDir, $conexionCreate->getDocument());
    
    // CREAMOS UN ARCHIVO DE CONFIGURACION QUE CARGARA EL ROOT (DIRECTORIO BASE)
    $configName = "config.php";
    $configDir = "./";
    $config = new ConfigCreator();
    $file->create($configName, $configDir, $config->getDocument());

    // CREAMO EL ERCHIVO PARA QUE CARGUEN AUTOMATICAMENTE LAS CLASES
    $autoLoaderName = "AutoLoader.php";
    $autoLoaderDir = "AutoLoader/";
    $autoLoader = new AutoLoaderCreator();
    $file->create($autoLoaderName, $autoLoaderDir, $autoLoader->getDocument());

    header("Location: tableList.php");
}
?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
        <meta name="generator" content="Jekyll v3.8.5">
        <title>Generador de clases y metodos - Genereitor</title>
        <!-- Bootstrap core CSS -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
        <link rel="stylesheet" href="css/style.css"/>
    </head>
    <body>
        <?php include_once 'includes/navbar.php'; ?>
        <!-- end navbar -->
        <main role="main">

            <div class="container">

                <div class="row">

                    <div class="col-md-12">

                        <h2>PASO 1 - Configuramos la conexion a la Base de Datos</h2>
                        <hr>


                        <form method="post">
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label for="Host">Host Name</label>
                                    <input type="text" name="hostName" class="form-control" id="Host" placeholder="Host Name" required/>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="User">User</label>
                                    <input type="text" name="user" class="form-control" id="User" placeholder="User" required/>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="inputPassword4">Password</label>
                                    <input type="password" name="password" class="form-control" id="inputPassword4" placeholder="Password">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="ddbb">Data Base Name</label>
                                <input type="text" name="bbddName" class="form-control" id="ddbb" placeholder="Data Base Name"  required/>
                            </div>
                            <input name="newConexion" value="Crear Conexion" type="submit" class="btn btn-primary"/>
                        </form>


                    </div>

                </div>
                <!-- end row -->
                <hr>

            </div> <!-- /container -->

        </main>
        <!-- end main -->

        <footer class="container">
            <p>&copy; Company 2017-2018</p>
        </footer>
        <!-- end footer -->
        <?php include_once 'includes/scripts.php'; ?>
    </body>
</html>