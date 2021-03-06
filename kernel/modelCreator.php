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
class modelCreator extends baseCreator {

    protected $document;
    protected $toStringField;

    /**
     * 
     * @return type
     */
    function getToString() {
        return $this->toStringField;
    }

    /**
     * 
     * @param type $toStringField
     */
    function setToString($toStringField) {
        $this->toStringField = $toStringField;
    }

    /**
     * 
     * @param type $param
     * @return string
     */
    function justText($param): string {
        return preg_replace('/[^A-Za-z]/', "", $param);
    }

    function getIdBaseCreate() {
        $content = "\n";
        $content .= <<<'EOD'
    /*
    * @return int pk de la tabla
    */
    public function getId(){
        $params = $this->getAllParams();
        return $params[0];
    }   
EOD;
    }

    /*
     * crea un metodo perteneciente a la clase abstracta ModelBase
     * que obtiene todos los paramentros de la clase
     * @return string
     */

    function getAllParamsBaseCreate(): string {
        $content = "\n";
        $content .= <<<'EOD'
    /*
    * @return array indexado con los nombre de todas las propiedades de la clase
    */
    public function getAllParams($excludeFK = false, $justKeys = true): array {
        $ret = get_object_vars($this);        
        if($justKeys){ $ret = array_keys($ret); }
        
        if($excludeFK){ $ret = array_slice($ret, 1); }
        
        return $ret;
    }
EOD;
        $content .= "\n";
        return $content;
    }

    /*
     * crea un metodo perteneciente a la clase abstracta ModelBase
     * que setea todos los paramentros de la clase
     * @return string
     */

    function setAllParamsBaseCreate(): string {
        $content = "\n";
        $content .= <<<'EOD'
    /*
    * @param array asociativo propiedadClase => valor $paramList
    * ej: ['nombre' => 'juan', 'telefono' => 987987987]
    */
    public function setAllParams(array $paramList){
        if(!empty($paramList)){
            foreach($paramList as $param => $value){
                if(property_exists($this, $param)){
                    //$this->$param = $value;
                    $method = "set$param";
                    $this->$method($value);
                }
            }
        }
        return $this;
    }
EOD;
        $content .= "\n";
        return $content;
    }

    /**
     * 
     * @return string
     */
    function baseModelClassCreate(): string {
        $content = "\n";
        $content .= "abstract class ModelBase {\n";
        $content .= $this->getIdBaseCreate();
        $content .= $this->getAllParamsBaseCreate();
        $content .= $this->setAllParamsBaseCreate();
        return "<?php\n{$content}\n}";
    }

    /**
     * crea la lista de parametros de la clase
     * @return string
     */
    function paramCreate(): string {
        $tab = $this->tab;
        if (!empty($this->getParamList())) {
            $ret = "";
            foreach ($this->getParamList() as $field) {
                $ret .= $tab . "protected \${$field['Field']};\n";
            }
            return $ret . "\n";
        }
    }

    /**
     * 
     * @return string
     */
    function construcCreate(): string {
        $fieldList = $this->prepareFieldTypeToConstruct($this->getParamList());
        $tab = $this->tab;
        $paramList = [];
        $setParamList = [];
        $ret = $tab . "public function __construct(\n";
        foreach ($fieldList as $field) {
            $type = $field['Type'];
            $paramList[] = $tab . $tab . "\${$field['Field']} = {$field['Default']}";
            $setParamList[] = $tab . $tab . "\$this->set" . ucfirst($field['Field']) . "(\${$field['Field']});";
        }
        $ret .= implode(",\n", $paramList);
        $ret .= "\n$tab){\n";
        $ret .= implode("\n", $setParamList);
        $ret .= "\n$tab}\n";

        return $ret;
    }

    /**
     * 
     * @return string
     */
    function toStringCreate(): string {
        $tab = $this->tab;
        $ret = "\n";
        $ret .= $tab . "public function __toString(){\n";
        $ret .= $tab . $tab . "return \$this->{$this->toStringField};\n";
        $ret .= $tab . "}\n";

        return $ret;
    }

    /**
     * 
     * @return string
     */
    function gettersCreate(): string {
        $ret = "";
        $tab = $this->tab;
        foreach ($this->getParamList() as $param) {
            $field = ucfirst($param['Field']);
            $ret .= "\n";
            $ret .= $tab . "public function get$field(){ return \$this->{$param['Field']}; }\n";
        }

        return $ret;
    }
    
    /**
     * 
     * @return string
     */
    function getModelCreate(): string {
        $ret = "";
        $tab = $this->tab;
        $paramList = $this->getParamList(false, true, true);
        foreach ($paramList as $field){
            $id = substr($field['Field'], 0, 2);
            if($id == "id"){
                $modelName = substr($field['Field'], 2);
                $modelC = "{$modelName}Controller";
                $ret .= "\n";
                $ret .= $tab . "public function get{$modelName}(){\n";
                $ret .= $tab . $tab . "\${$modelC} = new {$modelC}();\n";
                $ret .= $tab . $tab . "\${$field['Field']} = \$this->getId{$modelName}();\n";
                $ret .= $tab . $tab . "\${$modelName}List = \${$modelC}->select([['{$field['Field']}', '=', \${$field['Field']}]]);\n";
                $ret .= $tab . $tab . "return \${$modelName}List[0];\n";
                $ret .= $tab . "}\n";
            }
            
        }        
        
        return $ret;
    }

    /**
     * 
     * @return string
     */
    function settersCreate(): string {
        $ret = "";
        $tab = $this->tab;
        $fieldList = $this->prepareFieldTypeToConstruct($this->getParamList());
        foreach ($fieldList as $param) {
            $field = ucfirst($param['Field']);
            $ret .= "\n";
            $ret .= $tab . "public function set$field(\${$param['Field']} = {$param['Default']}){\n";
            $ret .= $tab . $tab . "\$this->{$param['Field']} = ({$param['Type']}) \${$param['Field']}; return \$this;\n";
            $ret .= $tab . "}\n";
        }

        return $ret;
    }

    /**
     * 
     * @return string
     */
    function classBaseCreate(): string {
        $className = ucfirst($this->className);
        $content = "\n";
        $content .= "abstract class {$className}Base extends ModelBase {\n\n";
        $content .= $this->paramCreate();
        $content .= $this->construcCreate();
        $content .= $this->toStringField != NULL ? $this->toStringCreate() : ""; // si hay un campo para toString entonces lo incluimos
        $content .= $this->gettersCreate();
        $content .= $this->getModelCreate();
        $content .= $this->settersCreate();
        $this->document = "<?php\n" . $content . "\n}";
        return $this->document;
    }
    
    /**
     * 
     * @return string
     */
    function classCreate(): string {
        $className = ucfirst($this->className);
        $content = "\n";
        $content .= "class {$className} extends {$className}Base {}";
        $this->document = "<?php\n" . $content;
        return $this->document;
    }

}
