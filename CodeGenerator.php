<?php
/**
 * Created by PhpStorm.
 * User: matri
 * Date: 16.02.2019
 * Time: 13:20
 */

namespace Common;


use Core\Migration;

class CodeGenerator
{
    function generate($name){
        $table=$this->getTable($name);
        dump($table);
    }
    function getTable($name){
        $migration=new Migration();
        return  $migration->readNewStructure()[$name];
    }
}