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
class controllerCreator extends baseCreator {

    protected function filterSqlPrepareCreate() {
        $content = "\n";
        $content .= <<<'EOD'
    /**
     * pasamos los parametros para condicionar el WHERE de la consulta
     * el aparametro debe de ser un array de arrays, cada array elemento tiene que ser de la forma:
     * ['campo_a_comparar', 'sigo_comparacion', 'valor_a_comparar', (opcional 'and | or')]
     * ejemplo: $filters = [['idEstado', 14], ['idTipo', 3, '!=', 'or'], ['marNombre', 'merce', 'like'], ['idBlog', '1,5,8', 'in']];
     * @param array $filters
     * @return string
     */
    protected function filterSqlPrepare(array $filters): string {
        $sql = "";
        $this->parameters = [];
        if(!empty($filters)){
            $sql .= " WHERE TRUE";
            $i = 1;
            $p = 'p';
            foreach($filters as $filter){
            	$field = $filter[0];
                $comparator = isset($filter[2]) ? strtoupper(trim($filter[2])) : "=";
            	$value = "LIKE" == $comparator ? "%{$filter[1]}%" : $filter[1];
                $united = (isset($filter[3]) && is_string($filter[3])) ? strtoupper($filter[3]) : 'AND';
                
                if('IN' != $comparator) {
                    $binParam = $p.$i;
                    $sql .= " $united {$field} {$comparator} :{$binParam}";
                    $this->parameters[$binParam] = $value;
                } else {
                    $value = str_replace(" ", "", $value);
                    $values = explode(",", $value);
                    $paramIN = [];
                    foreach($values as $val){
                        $binParam = $p.$i;
                        $paramIN[]= ":{$binParam}";
                        $this->parameters[$binParam] = $val;
                        $i++;
                    }
                    $finalParam = implode(", ", $paramIN);
                    $sql .= " $united {$field} {$comparator} ({$finalParam})";
                }
                $i++;
            }
        }
        return $sql;
    }
EOD;
        $content .= "\n";
        return $content;
    }

    protected function orderSqlPrepareCreate() {
        $content = "\n";
        $content .= <<<'EOD'
    /**
     * preparamos para ordenas la consulta
     * ejemplo: $ordenados = [['idTipo', 'desc'], ['fechaAlta']];
     * @param array $orderList
     * @return string
     */
    protected function orderSqlPrepare(array $orderList): string {
       $ret = "";
       if(!empty($orderList)){
           $sql = [];
           foreach($orderList as $order){
               $sql[] = isset($order[1]) ? $order[0].' '.strtoupper($order[1]) : $order[0];
           }
           $ret .=" ORDER BY ".implode(', ', $sql);
       }
       return $ret;
    }
EOD;
        $content .= "\n";
        return $content;
    }

    /**
     * 
     * @return string
     */
    protected function limitSqlPrepareCreate() {
        $content = "\n";
        $content .= <<<'EOD'
    /**
     * preparamos para limitar la consulta
     * ejemplo: $limitar = [2, 5] o $limitar = [3];
     * @param array $limit
     * @return string
     */
    protected function limitSqlPrepare(array $limit): string {
        $sql = "";
        if(!empty($limit)){
            $sql .= " LIMIT ";
            $sql .= isset($limit[1]) ? $limit[0].", ".$limit[1] : $limit[0];            
        }
        return $sql;
    }
EOD;
        $content .= "\n";
        return $content;
    }
    
    protected function groupSqlPrepareCreate() {
        $content = "\n";
        $content .= <<<'EOD'
    /**
     * Preparamos para agrupar la consulta por los campos indicados
     * ejemplo: $groupList = ['idCategoria', 'precio'];
     * @param array $groupList
     * @return string
     */
    protected function groupSqlPrepare(array $groupList){
        $ret = "";
        if(!empty($groupList)){
            $ret .=" GROUP BY ".implode(', ', $groupList);
        }
        return $ret;
    }
EOD;
        $content .= "\n";
        return $content;        
    }

    /**
     * 
     * @return type
     */
    function baseControllerClassCreate() {
        $content = "\n\n";
        $content .= "abstract class BaseController {\n\n";
        $content .= "   protected \$parameters = [];\n";
        $content .= $this->filterSqlPrepareCreate();
        $content .= $this->groupSqlPrepareCreate();
        $content .= $this->orderSqlPrepareCreate();
        $content .= $this->limitSqlPrepareCreate();
        $content .= $this->queryControllerCreate();
        return "<?php\n{$content}\n}";
    }

    /**
     * 
     * @return string
     */
    protected function bindValuePrepare($excudeAutoI = false) {
        $className = $this->getClassName();
        $fieldList = $this->getParamList(true, $excudeAutoI, true);
        $ret = "";
        foreach ($fieldList as $field) {
            $ret .= "\$statement->bindValue(\":{$field}\", \${$className}->get" . ucfirst($field) . "());\n";
        }
        return $ret;
    }
    
    

    protected function updateFieldPrepare() {
        $fieldList = $this->getParamList(true, true, true);
        $setFields = [];        
        $ret = "";
        foreach($fieldList as $field){
            $setFields[] = "{$field} = :{$field}";
        }
        $ret .= implode(", ", $setFields);
        return $ret;
    }

    /*
     * TODO: hacer que los fields, los valores y los bindValues se creen con el metodo getAllParams() del modelo que recive
     * ver como esta hecho el $this->bindValuePrepare() para hacerlo igual
     */

    function insertControllerCreate() {
        $className = $this->getClassName();
        $tableName = $this->getTableName();
        $fieldList = $this->getParamList(true, true, true);
        $fields = implode(", ", $fieldList);
        $values = implode(", :", $fieldList);
        $content = "\n\n";
        $content .= <<<"EOD"
    public function insert($className \$$className): int {
        try{
            \$sql = "INSERT INTO {$tableName} ({$fields}) 
            VALUES (:{$values});";
            \$conexion = new Conexion();
            \$statement = \$conexion->pdo()->prepare(\$sql);
            {$this->bindValuePrepare(true)}
            \$ret = 0;
            if(\$statement->execute()){
                \$ret = \$conexion->pdo()->lastInsertId();
                \$conexion = NULL;
                \$statement->closeCursor();
            }
            return \$ret;

        } catch (Exception \$ex){
            echo "[ERROR] -> {\$ex->getMessage()} [ERROR CODE] -> {\$ex->getCode()}";
        }
    }
EOD;
        return $content;
    }
    
    function updateControllerCreate() {
        $className = $this->getClassName();
        $tableName = $this->getTableName();
        $primariKeys = $this->getPrimariKeys(true);
        $fields = $this->updateFieldPrepare();
        $whereFields = [];
        foreach ($primariKeys as $field) {
            $whereFields[] = "{$field} = :{$field}";
        }
        $where = "WHERE " . implode(" AND ", $whereFields) ." LIMIT 1;";
        $content = "\n\n";
        $content .= <<<"EOD"
    public function update($className \$$className): int {
        try{
            \$sql = "UPDATE {$tableName} SET {$fields} {$where}";
            \$conexion = new Conexion();
            \$statement = \$conexion->pdo()->prepare(\$sql);
            {$this->bindValuePrepare()}
            \$ret = 0;
            if(\$statement->execute()){
                \$ret = \$statement->rowCount();
                \$conexion = NULL;
                \$statement->closeCursor();
            }
            return \$ret;

        } catch (Exception \$ex){
            echo "[ERROR] -> {\$ex->getMessage()} [ERROR CODE] -> {\$ex->getCode()}";
        }
    }
EOD;
        return $content;
    }
    
    function selectControllerCreate() {
        $className = $this->getClassName();
        $tableName = $this->getTableName();
        $fieldList = $this->getParamList(true);
        $rowFiels = [];
        foreach ($fieldList as $field){
            $rowFiels[] = "\$row->$field";
        }
        $fields = implode(", ", $fieldList);
        $fieldObject = implode(", ", $rowFiels);
        $content = "\n\n";
        $content .= <<<"EOD"
    public function select(array \$filtros = [], array \$ordenados = [], array \$limitar = [], array \$agrupar = []): array {
        try{
            \$sql = "SELECT {$fields} 
            FROM {$tableName}";                        
            \$ret = [];
            \$rows = \$this->query(\$sql, \$filtros, \$ordenados, \$limitar, \$agrupar);
            
            if(!empty(\$rows)){
                foreach(\$rows as \$row){
                    \$ret[] = new {$className}({$fieldObject});
                }
            }
            
            return \$ret;

        } catch (Exception \$ex){
            echo "[ERROR] -> {\$ex->getMessage()} [ERROR CODE] -> {\$ex->getCode()}";
        }
    }
EOD;
        return $content;
    }
    
    
    function queryControllerCreate() {
        $content = "\n\n";
        $content .= <<<'EOD'
    /**
     * Para realizar querys personalizadas
     * @param string $sql
     * @param array $filtros
     * @param array $ordenados
     * @param array $limitar
     * @param array $agrupar
     * @return array
     */
    protected function query(string $sql, array $filtros = [], array $ordenados = [], array $limitar = [], array $agrupar = []): array {
        try{
            $sql .= $this->filterSqlPrepare($filtros);
            $sql .= $this->groupSqlPrepare($agrupar);
            $sql .= $this->orderSqlPrepare($ordenados);
            $sql .= $this->limitSqlPrepare($limitar);
            $conexion = new Conexion();
            $statement = $conexion->pdo()->prepare($sql);
            
            foreach($this->parameters as $key => $val){
		$statement->bindValue($key, $val);
            }
                        
            $ret = [];
            if($statement->execute() and $statement->rowCount() > 0){
                $ret = $statement->fetchAll(PDO::FETCH_OBJ);
            }
            
            $conexion = NULL;
            $statement->closeCursor();
            
            return $ret;

        } catch (Exception $ex){
            echo "[ERROR] -> {$ex->getMessage()} [ERROR CODE] -> {$ex->getCode()}";
        }
    }
EOD;
        return $content;
    }
    
    
    function deleteControllerCreate() {
        $tableName = $this->getTableName();
        $primariKeys = $this->getPrimariKeys(true);
        $whereFields = [];
        $validateFiels = [];
        foreach ($primariKeys as $field) {
            $whereFields[] = "{$field} = :{$field}";
            $validateFiels[] = "!isset(\$ids['{$field}'])";
        }
        $where = " WHERE " . implode(" AND ", $whereFields) ." LIMIT 1;";
        $isset = implode(" or ", $validateFiels);
        $content = "\n\n";
        $content .= <<<"EOD"
    public function deleteByIds(array \$ids = []): int {
        try{
            if({$isset}){
                throw new Exception('Para eliminar un registro, se tiene que especificar sus ids');
            }
            \$sql = "DELETE FROM {$tableName}";
            \$sql .= "{$where}";
            \$conexion = new Conexion();
            \$statement = \$conexion->pdo()->prepare(\$sql);
            
            foreach(\$ids as \$key => \$value){
                \$statement->bindValue(":{\$key}", \$value);
            }
            
            \$ret = 0;
            if(\$statement->execute()){
                \$ret = \$statement->rowCount();
                \$conexion = NULL;
                \$statement->closeCursor();
            }
            return \$ret;

        } catch (Exception \$ex){
            echo "[ERROR] -> {\$ex->getMessage()} [ERROR CODE] -> {\$ex->getCode()}";
        }
    }
EOD;
        return $content;
    }

    function controllerBaseCreate(): string {
        $className = ucfirst($this->getClassName());
        $content = "\n";
        $content .= "abstract class {$className}BaseController extends BaseController {\n\n";
        $content .= $this->insertControllerCreate();
        $content .= $this->updateControllerCreate();
        $content .= $this->selectControllerCreate();
        $content .= $this->deleteControllerCreate();
        $this->document = "<?php\n" . $content . "\n}";
        return $this->document;
    }
    
    function controllerCreate(): string {
        $className = ucfirst($this->getClassName());
        $content = "\n";
        $content .= "class {$className}Controller extends {$className}BaseController { }";
        $this->document = "<?php\n" . $content;
        return $this->document;
    }

}
