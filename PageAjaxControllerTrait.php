<?php

namespace CommonBase;

trait PageAjaxControllerTrait
{

    public function hasPermission()
    {
        return \Authorization\Authorization::isLogged();
    }
}
