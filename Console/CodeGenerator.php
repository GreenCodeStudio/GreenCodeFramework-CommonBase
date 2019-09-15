<?php

namespace CommonBase\Console;

use Core\AbstractController;

class CodeGenerator extends AbstractController
{

    function generate($namespace, $name, $dbName)
    {
        $CodeGenerator = new \CommonBase\CodeGenerator();
        $CodeGenerator->generate($namespace, $name, $dbName);
    }
}
