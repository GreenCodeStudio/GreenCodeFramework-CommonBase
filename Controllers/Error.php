<?php

namespace CommonBase\Controllers;


class Error extends \Common\PageStandardController
{
    function index($debugOutput)
    {
        $this->addViewString($debugOutput);
    }

}