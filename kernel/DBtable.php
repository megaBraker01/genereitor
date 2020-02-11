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
 * Description of DBtable
 * se encarga de leer y manipular la informacion de todas las tablas de la bbdd 
 * @author Rafael Perez Sanchez <angel_rafael01@hotmail.com>
 */
require_once 'AutoLoader/AutoLoader.php';

class DBtable {

    /**
     * devuelve la lista de tablas de la Base de Datos
     * @return array
     */
    function getTableList($tableNames = []) {
        try {
            $conexion = new Conexion;
            $query = "SELECT * FROM information_schema.TABLES WHERE TABLE_SCHEMA ='" . $conexion::DATABASE . "'";
            if (!empty($tableNames)) {
                $prepareToquery = "";
                foreach ($tableNames as $table) {
                    $prepareToquery .= "'$table',";
                }
                $prepareToquery = trim($prepareToquery, ",");
                $query .= " AND TABLE_NAME IN ($prepareToquery)";
            }
            $result = $conexion->pdo()->prepare($query);
            $result->execute();
            $list = [];
            if ($result->rowCount() > 0) {
                while ($fila = $result->fetch(PDO::FETCH_ASSOC)) {
                    $list[] = $fila['TABLE_NAME'];
                }
                $conexion = NULL;
                $result->closeCursor();
            }
            return $list;
        } catch (Exception $ex) {
            return '[ERROR] -> ' . $ex->getMessage() . "<br> [ERROR CODE] -> " . $ex->getCode();
        }
    }

    /**
     * devuelve la lista de arrays con los nombres y atributos de todos los campos de una tabla
     * atributos:
     * Field = nombre del campo
     * Type = tipo (longitud)
     * Null = YES o NO
     * Key = PRI si es clave primaria
     * Default = valor por defecto
     * Extra = si es auto-incrementado etc..
     * @return array 
     */
    function getTableFields($tableName) {
        try {
            $query = "DESCRIBE $tableName";
            $conexion = new Conexion;
            $result = $conexion->pdo()->prepare($query);
            $result->execute();
            $list = [];
            if ($result->rowCount() > 0) {
                while ($fila = $result->fetch(PDO::FETCH_ASSOC)) {
                    $list[] = $fila;
                }
                $conexion = NULL;
                $result->closeCursor();
            }
            return $list;
        } catch (Exception $exc) {
            echo $exc->getTraceAsString() . " [MENSAJE] " . $ex->getMessage();
        }
    }

}
