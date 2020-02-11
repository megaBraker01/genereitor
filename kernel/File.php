<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CreateFile
 *
 * @author Rafael Perez Sanchez <angel_rafael01@hotmail.com>
 */
class File {

    public function create($name = "newFile.txt", $path = "", $content = "") {
        if ($path == "") {
            $path = getcwd();
        }
        if (!file_exists($path)) {
            mkdir($path);
        }
        $fullPath = "$path/$name";
        try {
            if ($file = fopen($fullPath, "w")) {
                if ($content != "") {
                    fwrite($file, $content);
                }
                fclose($file);
                return TRUE;
            }
            return FALSE;
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
        }
    }

    public function edit($name, $content) {
        $file = fopen($name, 'w');
        if (fwrite($file, $content)) {
            fclose($file);
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function delete($name) {
        return unlink($name);
    }

}
