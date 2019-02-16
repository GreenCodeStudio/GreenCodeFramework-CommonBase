<?php

namespace Common\Console;

class CodeGenerator extends \Core\AbstractController
{

    function generate($name)
    {
        $CodeGenerator = new \Common\CodeGenerator();
        $CodeGenerator->generate($name);
    }
}
