<?php

namespace Common\Controllers;

class Start extends \Common\PageStandardController
{

    function index()
    {
        $this->addView('Common', 'start');
    }

    function demo($a, int $b = 5, \Exception $c = null)
    {
        dump($a, $b);
    }

    function formDemo()
    {
        $this->addView('Common', 'formDemo');
    }

    function formDemo_data()
    {
        return ['demo' => ['test1' => rand(10, 20)]];
    }

    function tableDemo()
    {
        $this->addView('Common', 'tableDemo');
    }

    function errorDemo()
    {
        dump(123);
        throw new \Exception('abcd');
    }

}
