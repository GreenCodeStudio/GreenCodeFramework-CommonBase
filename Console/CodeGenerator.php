<?php

namespace CommonBase\Console;

use Core\AbstractController;

class CodeGenerator extends AbstractController
{

    function generate($namespace, $name)
    {
        $CodeGenerator = new \Common\CodeGenerator();
        $CodeGenerator->generate($namespace, $name);
    }
}
