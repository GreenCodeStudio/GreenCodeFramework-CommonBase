<?php

namespace Common\Controllers;

class Start extends \Core\StandardController {

    function index() {
        $this->addView('Common', 'start');
    }

    function demo($a, int $b = 5, \Exception $c = null) {
        dump($a, $b);
    }

}
