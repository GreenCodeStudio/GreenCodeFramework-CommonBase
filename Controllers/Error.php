<?php

namespace Common\Controllers;


class Error extends \Common\PageStandardController
{
    function index($debugOutput)
    {
        $this->addViewString($debugOutput);
    }

}