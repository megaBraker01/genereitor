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

if (isset($_GET['tablaList']) and $_GET['tablaList'] != "") {
    $tablas = new DBtable;
    // obtenemos la lista de tablas seleccionadas mandando como paramentro un array
    // que se optiene por el explode separando por coma (,) 
    $tablaList = $tablas->getTableList(explode(",", $_GET['tablaList']));
}

if (isset($_POST['className'])) {
    
    if (!isset($_SESSION)) { session_start(); } // creamos una sesion que guardara los nombre de las tablas y el nombre asignado para clase modelo correspondiente
    unset($_POST['className']); // eliminamos la posicion className porque no queremos que se guarde en la sesion
    $_SESSION['className'] = $_POST;
    header("Location: selectFieldToString.php?tablaList=" . implode(",", $tablaList));
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
        <title>Selecciona el nombre de la clase modelo - Genereitor</title>
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
                    <div class="col-md-6">
                        <?php if (isset($tablaList) and count($tablaList) > 0) { // si hay registros  ?>

                            <div class="card">
                                <div class="card-header">
                                    <h2>Selecciona el nombre de la clase modelo</h2>
                                </div>
                                <div class="card-body">
                                    <p>
                                    <form method="post" id="form1" name="generar">
                                        <input type="submit" form="form1" name="className" class="btn btn-primary btn-sm"/>
                                    </form>
                                    </p>
                                    <table class="table table-striped table-bordered table-hover table-sm">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th scope="col">Nombre tabla</th>
                                                <th scope="col">Nombre que tendrá la clase</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $i = 1;
                                            foreach ($tablaList as $tabla) {
                                                $fields = $tablas->getTableFields($tabla);
                                                $id = "check-$i";
                                                $i++;
                                                ?>
                                                <tr>
                                                    <td><label for="<?= $id ?>"><?= $tabla ?></label> </td>
                                                    <td>
                                                        <input type="text" name="<?= $tabla ?>" id="<?= $id ?>" value="<?= $tabla ?>" form="form1" class="form-control form-control-sm"/>
                                                    </td>
                                                </tr>
                                            <?php } // fin lista de tablas  ?>
                                        </tbody>
                                    </table>
                                    <input type="submit" form="form1" name="className" class="btn btn-primary btn-sm"/>
                                </div>
                            </div>
                        <?php } // fin si hay registros  ?>
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