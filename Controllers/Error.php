<?php

namespace CommonBase\Controllers;


use Common\PageStandardController;

class Error extends PageStandardController
{
    function index($debugOutput)
    {
        $this->addViewString($debugOutput);
    }

}