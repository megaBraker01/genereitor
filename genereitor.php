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
 * @author Rafael Perez Sanchez <angel_rafael01@hotmail.com>
 */

require_once 'config.php';
require_once "kernel/autoLoader.php";

if (!isset($_SESSION)) { session_start(); }

function normalizeClassName($str){
    $strlower = strtolower($str);
    $strNoScore = str_replace("_", " ", $strlower);
    $strupper = ucwords($strNoScore);
    $strNoSpace = str_replace(" ", "", $strupper);
    return $strNoSpace;
}

// reorganizamos la informacion en un unico array llamado tablesInfo
if (isset($_SESSION['className'])){
    $tableInfo = [];
    foreach ($_SESSION['className'] as $key => $value){

        $tableInfo['tableName'] = $key;
        $tableInfo['className'] = $value;

        foreach ($_SESSION['toString'] as $k => $v){
            if($key == $k){
                $tableInfo['toString'] = $v;
            }
        }

        $_SESSION['tablesInfo'][] = $tableInfo;

    }

     unset($_SESSION['className']); // eliminamos esta posicion porque ya no hace falta
     unset($_SESSION['toString']); // eliminamos esta posicion porque ya no hace falta

    /*
     sabiendo el parametro que se va a seleccionar como toString de la clase
     y sabiendo el nombre que va a tener la clase modelo
     ya podemos crear sus correspondiente modelo y controlador
     empezando por el modelo
    */

    $tablaController = new DBtable; // creamos el objeto para obtener la informacion de la tabla
    $modelPath = "Modelo/"; // direccion donde irán las clases modelo
    $controllerPath = "Controlador/"; // direccion donde irán las clase controller
    $file = new File;
    foreach($_SESSION['tablesInfo'] as $tablesInfo){
        $tableName = $tablesInfo['tableName'];
        $className = $tablesInfo['className'];
        $toStringField = $tablesInfo['toString'];
        $modelCreator = new modelCreator($className, $tableName);
        if ($toStringField != "-") {
            $modelCreator->setToString($toStringField);
        }
        $fields = $tablaController->getTableFields($tableName); // obtenemos los campos de la tabla desde la bbdd
        // TODO: cambiar el nombre de setParamList a setFieldList (en baseCreator tambien)
        $modelCreator->setParamList($fields);
        $modelContent = $modelCreator->classCreate();
        $modelBaseContent = $modelCreator->classBaseCreate();
        $classNameNormalizer = $modelCreator->normalizeClassName($className);
        $file->create($classNameNormalizer."Base.php", $modelPath."base", $modelBaseContent); // creamos la clase Base en el directorio especificado
        $file->create($classNameNormalizer.".php", $modelPath, $modelContent); // creamos la clase en el directorio especificado

        // ahora creamos los controladores
        $controller = new controllerCreator($className, $tableName);
        $controller->setParamList($fields);
        $controllerContent = $controller->controllerCreate();
        $controllerBaseContent = $controller->controllerBaseCreate();
        $file->create($classNameNormalizer."Controller.php", $controllerPath, $controllerContent);
        $file->create($classNameNormalizer."BaseController.php", $controllerPath."base", $controllerBaseContent);
        echo("<pre>$controllerContent</pre>");

    }
    $file->create('ModelBase.php', $modelPath."/base", $modelCreator->baseModelClassCreate()); // creamos la clase ModelBase.php
    $file->create("BaseController.php", $controllerPath."/base", $controller->baseControllerClassCreate()); // creamos la clase BaseContro
}
 unset($_SESSION);
 session_unset();
 session_destroy();
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

                        <div class="card">
                            <div class="card-header">
                                <h2>Titulo</h2>
                            </div>
                        </div>
                        <div class="card-body">

                            <!-- aqui van los elementos -->
                            <h3>La clase se ha creado correctamente</h3>


                        </div>

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