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
require_once 'config.php';
require_once "kernel/autoLoader.php";

if (isset($_POST['generar'])) {
    $tablaList = [];
    // para cada tabla seleccionada, la introducimos en una lista (array)
    foreach ($_POST as $key => $value) {
        if ($value == 'on') {
            $tablaList[] = $key;
        }
    }
    // mandamos por GET la lista de tablas separadas por coma ','
    header("Location: selectFieldForModel.php?tablaList=" . implode(",", $tablaList));
}

$tablas = new DBtable;
$tablaList = $tablas->getTableList();
?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
        <meta name="generator" content="Jekyll v3.8.5">
        <title>Lista de Tablas - Genereitor</title>
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
                        <div class="card">
                            <div class="card-header">
                                <h2>Tablas de la Base de Datos</h2>
                            </div>
                            <div class="card-body">
                                <?php if (isset($tablaList) and count($tablaList) > 0) { // si hay registros  ?>
                                    <p>
                                    <form method="post" id="generar" name="generar">
                                        <button type="button" class="btn btn-info btn-sm" onclick="seleccionar_todo();">Seleccionar Todas</button>
                                        <input type="submit" name="generar" value="Generar" class="btn btn-primary btn-sm"/>
                                    </form>
                                    </p>
                                    <table class="table table-striped table-bordered table-hover table-sm">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th scope="col">Seleccionar</th>
                                                <th scope="col">Nombre tabla</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $i = 1;
                                            foreach ($tablaList as $tabla) {
                                                $id = "check-$i";
                                                $i++;
                                                ?>
                                                <tr>
                                                    <th scope="row"><input type="checkbox" form="generar" id="<?= $id ?>" name="<?= $tabla ?>"/></th>
                                                    <td><label for="<?= $id ?>"><?= $tabla ?></label></td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                    <button type="button" class="btn btn-info btn-sm" onclick="seleccionar_todo();">Seleccionar Todas</button>
                                    <input type="submit" form="generar" name="generar" value="Generar" class="btn btn-primary btn-sm"/>
                                <?php } // fin si hay registros ?>

                                <?php if (count($tablaList) == 0) { // si no hay registros ?>
                                    <h2>No hay registros!!</h2>    
                                <?php } // fin si no hay registros?>

                            </div>
                        </div>
                    </div>
                </div>
                <!-- end row -->
                <hr>
            </div> <!-- /container -->
        </main>
        <!-- end main -->
        <footer class="container">
            <p>&copy; Company <?php echo date('Y'); ?></p>
        </footer>
        <!-- end footer -->
        <script>
            select = 1;
            function seleccionar_todo() {
                let form = document.generar;

                if (select == 1) {
                    select = 0;
                    console.log(form.elements[0]);
                    //form.elements[0].innerText = "Desmarcar Todas";
                    for (i = 0; i < form.length; i++)
                        if (form.elements[i].type == "checkbox")
                            form.elements[i].checked = 1;
                } else {
                    select = 1;
                    //form.elements[0].innerText = 'Seleccionar Todas';
                    for (i = 0; i < form.length; i++)
                        if (form.elements[i].type == "checkbox")
                            form.elements[i].checked = 0;
                }
            }
        </script>
        <?php include_once 'includes/scripts.php'; ?>
    </body>
</html>