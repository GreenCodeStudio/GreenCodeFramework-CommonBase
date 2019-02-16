<?php

namespace Common\Console;

class CodeGenerator extends \Core\AbstractController
{

    function generate($namespace, $name)
    {
        $CodeGenerator = new \Common\CodeGenerator();
        $CodeGenerator->generate($namespace, $name);
    }
}
