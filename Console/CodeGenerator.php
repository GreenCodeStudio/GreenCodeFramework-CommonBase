<?php

namespace CommonBase\Console;

use Core\AbstractController;

class CodeGenerator extends AbstractController
{

    function generate($namespace, $name, $dbName)
    {
        $CodeGenerator = new \Common\CodeGenerator();
        $CodeGenerator->generate($namespace, $name, $dbName);
    }
}
