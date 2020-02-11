<?php

/*
 * AutoLoader es una funcion que se ejecuta automaticamente cuando intentamos utilizar
 * una clase que NO se encuentra en el mismo fichero desde donde se quiere utilizar,
 * esta funcion toma como parametro el nombre de la clase que se intenta instanciar.
 */

if (!function_exists('autoLoader')) {

    function autoLoader($class) {
        $currentDir = "./";
        $parentDir = "../";
        $classFile = $class . '.php';
        $clasWasFound = false;

        // PRIMERO - buscamos la clase en el directorio actual
        if (is_file($classFile) && !class_exists($class)) {
            require_once($classFile);
            $clasWasFound = true;
        }

        // SEGUNDO - si no está, identificamos los directorios hijos para buscar entre ellos        
        if (!$clasWasFound) {
            $scanCurrentDirList = array_slice(scandir($currentDir), 2); // listamos los directorios que estan dentro del directorio actual
            foreach ($scanCurrentDirList as $dir) {
                if (is_dir($dir)) {
                    $fullPath = $dir . "/" . $classFile;
                    if (is_file($fullPath) && !class_exists($class)) {
                        require_once($fullPath);
                        $clasWasFound = true;
                        break;
                    }
                }
            }
        }

        // TERCERO - si no está, buscamos en el directorio padre
        if (!$clasWasFound) {
            if (is_file($parentDir . $classFile) && !class_exists($class)) {
                require_once($parentDir . $classFile);
                $clasWasFound = true;
            }
        }

        // CUARTO - si no está, identificamos los directorios hermanos para buscar entre ellos        
        if (!$clasWasFound) {
            $scanParentDirList = array_slice(scandir($parentDir), 2);
            foreach ($scanParentDirList as $dir) {
                if (is_dir($parentDir . $dir)) {
                    $fullPath = $parentDir . $dir . "/" . $classFile;
                    if (is_file($fullPath) && !class_exists($class)) {
                        require_once($fullPath);
                        $clasWasFound = true;
                        break;
                    }
                }
            }
        }

        // QUINTO - si finalmente no esta la clase, paramos la ejecucion
        if (!$clasWasFound) {
            die("El archivo {$classFile} no se ha encontrado en los directorios especificados.");
        }
    }

}
spl_autoload_register('autoLoader');
