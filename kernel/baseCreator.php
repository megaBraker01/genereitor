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
 * Description of classCreator
 *
 * @author Rafael Perez Sanchez <angel_rafael01@hotmail.com>
 */
abstract class baseCreator {

    protected $document;
    protected $className;
    protected $tableName;
    protected $paramList;
    protected $tab = "    "; // 4 espacios en blanco para tabular

    function __construct($className, $tableName) {
        $this->setClassName($this->normalizeClassName($className));
        $this->setTableName($tableName);
    }
    
    function normalizeClassName($str){
        $strlower = strtolower($str);
        $strNoScore = str_replace(["_", "-"], " ", $strlower);
        $strupper = ucwords($strNoScore);
        $strNoSpace = str_replace(" ", "", $strupper);
        return $strNoSpace;
    }

    function justText($param): string {
        return preg_replace('/[^A-Za-z]/', "", $param);
    }

    function getClassName() {
        return $this->className;
    }
    
    function getTableName() {
        return $this->tableName;
    }

    /**
     * Obtiene la lista de todos los campos de la tabla
     * @param bool $justFields indica que sólo devuelve un array con los nombres de los campos
     * @param bool $excludeAutoI indica que excluye aquellos campos que sean autoincrementales
     * @param bool $excludeTimeStamp indica que excluye los campos de tipo timeStamp
     * @return array
     */
    function getParamList(bool $justFields = false, bool $excludeAutoI = false, bool $excludeTimeStamp = false): array {
        $fields = $this->paramList ?? [];
        $totalFields = count($fields);
        $ret = [];
        
        for($i = 0; $i < $totalFields; $i++){
            if($excludeAutoI and $fields[$i]['Extra'] == 'auto_increment'){
                unset($fields[$i]);
                continue;
            }
            
            if($excludeTimeStamp and $fields[$i]['Type'] == 'timestamp'){
                unset($fields[$i]);
                continue;
            }
        }
        
        foreach($fields as $field){
            $ret[] = $field;
        }
        
        if($justFields){
            $ret = array_column($ret, 'Field');
        }
        
        return $ret;
    }
    
    protected function getPrimariKeys($justFields = false){
        $ret = [];
        foreach($this->getParamList() as $field){
            if($field['Key'] == 'PRI'){
                $ret[] = $field;
            }
        }
        
        if($justFields){
            $ret = array_column($ret, 'Field');
        }
        
        return $ret;
    }

    function setClassName($className) {
        $this->className = ucfirst($className);
        return $this;
    }
    
    function setTableName($tableName) {
        $this->tableName = $tableName;
        return $this;
    }

    function setParamList($params = []) {
        if (!empty($params)) {
            //$this->paramList = $this->prepareFieldTypeToConstruct($params);
            $this->paramList = $params;
        }
        return $this;
    }

    /*
     * @param: el array con la informacion de la tabla (decribe nombreTabla)
     * prepara el tipo de valor y el valor por defecto de todos los parametros del contructor
     * @return: un strign formateado
     */

    // TODO: ver si este metodo funciona correctamente seteando los tipos
    function prepareFieldTypeToConstruct($params = []): array {
        if (!empty($params)) {
            $newParam = [];
            foreach ($params as $field) {
                $newType = $this->justText($field['Type']);
                switch ($newType) {
                    case "int": case "tinyint": case "smallint":
                    case "mediumint": case "bigint":
                        $field['Type'] = "int";
                        $field['Default'] = (int) $field['Default'];
                        break;
                    case "varchar": case "char": case "tinytext": case "text":
                    case "mediumtext": case "longtext": case "date": case "datetime":
                    case "timestamp": case "time": case "year":
                        $field['Type'] = "string";
                        $field['Default'] = $field['Default'] == "CURRENT_TIMESTAMP" ? "''" : "'" . $field['Default'] . "'";
                        break;
                    case "decimal": case "float": case "double": case "real":
                        $field['Type'] = "float";
                        $field['Default'] = (float) $field['Default'];
                        break;
                    default :
                        $field['Type'] = "";
                        $field['Default'] = 'NULL';
                        break;
                }
                $newParam[] = $field;
            }
            return $newParam;
        }
    }

}
