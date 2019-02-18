<?php
/**
 * Created by PhpStorm.
 * User: matri
 * Date: 19.07.2018
 * Time: 10:20
 */

namespace Common\Controllers;


class Authorization extends \Common\PageStandardController
{
    public function index()
    {
        $this->addView('Common', 'login');
    }

    public function postAction()
    {
        require __DIR__.'/../Views/loginTemplate.php';
    }

    public function hasPermission()
    {
        return true;
    }
}