<?php

/*
 * AutoLoader es una funcion que se ejecuta automaticamente cuando intentamos utilizar
 * una clase que NO se encuentra en el mismo fichero desde donde se quiere utilizar,
 * esta funcion toma como parametro el nombre de la clase que se intenta instanciar.
 * 
 * NOTA: antes de hacer un include, include_once, require o require_once de este archivo,
 * se tiene que requerir (require_once) el fichero config.php que es quien define a la constante
 * SITE_ROOT
 * ejemplo
 * require_once '../config.php'
 * require_once '../AutoLoader/autoLoader.php 
 */

if (!function_exists('autoLoader')) {

    function autoLoader($class) {
        $siteRoot = SITE_ROOT;
        $currentDir = "./";
        $classFile = $class . '.php';
        $fullPath = $currentDir . "/" . $classFile;
        $clasWasFound = false;

        // PRIMERO - buscamos la clase en el directorio actual (desde donde se hace el require)
        if (is_file($fullPath)) {
            require_once($classFile);
            $clasWasFound = true;
        }
        
        /* SEGUNDO - si no está, buscamos desde el directorio raiz en los siguientes directorios:
         * - Conexion
         * - Controlador
         * - Modelo 
         */
        $principalDirectories = ['Conexion', 'Controlador', 'Modelo'];
        
        $i = 0;
        while (!$clasWasFound and isset($principalDirectories[$i])){
            $path = $siteRoot. "/" . $principalDirectories[$i] . "/" .$classFile;
            if (is_file($path)) {
                require_once($path);
                $clasWasFound = true;
            }
            
            /* Si no esta, Buscamos en el directorio "base" que se encuentra dentro de 
             * los directorios "Controlador" y "Modelo"
             */
            if(!$clasWasFound){
                $path = $siteRoot. "/" . $principalDirectories[$i] . "/base/" .$classFile;
                if (is_file($path)) {
                    require_once($path);
                    $clasWasFound = true;
                }
            }
            
            $i++;
        }
        
    }

}
spl_autoload_register('autoLoader');