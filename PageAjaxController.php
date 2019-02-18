<?php

namespace Common;

class PageAjaxController extends \Core\AjaxController
{

    public function hasPermission()
    {
        return \Authorization\Authorization::isLogged();
    }
}
