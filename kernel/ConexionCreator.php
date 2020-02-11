<?php

/*
 * Copyright 2019.
 *
 * Software protegido por la propiedad intelectual.
 * Queda prohibido copiar, modificar, fusionar, publicar, distribuir, sublicenciar y / o vender
 * copias no autorizadas del software
 *
 * El aviso de copyright anterior y este aviso de permiso se incluirán en todas las copias o 
 * porciones sustanciales del software.
 */

/**
 * se encarga de generar el codigo que tendrá la clase conexion
 *
 * @author Rafael Perez Sanchez <angel_rafael01@hotmail.com>
 */
class ConexionCreator {

    protected $param;
    protected $document = "<?php\n";

    function __construct($hostName, $user, $password, $ddbbName) {
        $this->param = [
            'HOSTNAME' => $hostName,
            'USER' => $user,
            'PASSWORD' => $password,
            'DATABASE' => $ddbbName
        ];
    }

    function paramCreate() {
        $content = "\n";
        foreach ($this->param as $key => $val) {
            $content .= "    const $key = '$val';\n";
        }
        return $content;
    }

    function classCreate() {
        $content = "\n";
        $content .= "class Conexion {\n" . $this->paramCreate();
        $content .= <<<'EOD'
    protected $conexion;
    
    public function __construct(){
        try{
            $this->conexion = new PDO("mysql:host=" .self::HOSTNAME. "; dbname=" .self::DATABASE, self::USER, self::PASSWORD); // realizamos la conexion
            $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);  // preparamos las excepciones
            $this->conexion->exec("SET CHARACTER SET utf8");  // especificamos la codificacion de la conexion
            
        } catch(Exception $e){ // validamos si hay algun error al conecar con la bbdd
            die("Fallo en la conexion: " .$e->GetMessage()); // avisamos del error
            exit(); // salimos 
        } 
    }
    
    public function pdo(){
        return $this->conexion;
    }
}
EOD;
        $this->document .= $content;
    }

    function getDocument() {
        return $this->document;
    }

}
