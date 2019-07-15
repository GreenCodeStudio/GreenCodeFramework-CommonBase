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

    public function token(string $token)
    {
        \Authorization\Authorization::loginByToken($token);
        header('Location:/');
        http_response_code(302);
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