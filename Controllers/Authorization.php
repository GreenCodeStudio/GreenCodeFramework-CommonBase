<?php
/**
 * Created by PhpStorm.
 * User: matri
 * Date: 19.07.2018
 * Time: 10:20
 */

namespace CommonBase\Controllers;


use Authorization\Exceptions\BadAuthorizationException;
use Authorization\Exceptions\ExpiredTokenException;
use Common\PageStandardController;

class Authorization extends PageStandardController
{
    public function index()
    {
        $this->addView('CommonBase', 'login');
    }

    /**
     * @throws BadAuthorizationException
     * @throws ExpiredTokenException
     */
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

    public function hasPermission(string $methodName)
    {
        return true;
    }
}