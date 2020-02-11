<?php

/*
 * Copyright 2020.
 *
 * Software protegido por la propiedad intelectual.
 * Queda prohibido copiar, modificar, fusionar, publicar, distribuir, sublicenciar y / o vender
 * copias no autorizadas del software
 *
 * El aviso de copyright anterior y este aviso de permiso se incluirán en todas las copias o 
 * porciones sustanciales del software.
 */

/**
 * Description of ConfigCreator
 *
 * @author @author Rafael Perez Sanchez <angel_rafael01@hotmail.com>
 */
class ConfigCreator {
        
    public function getDocument(){
        $content = "\n";
        $content .= <<<'EOD'
<?php
    
    /**
     * Este archivo se debe de colocar en la raiz de nuestro sitio web
     */
    define('SITE_ROOT', __DIR__);
EOD;
        return $content;
    }
}
