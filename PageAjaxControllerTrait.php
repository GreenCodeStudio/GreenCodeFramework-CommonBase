<?php

namespace CommonBase;

use Authorization\Authorization;

trait PageAjaxControllerTrait
{

    public function hasPermission()
    {
        return Authorization::isLogged();
    }
}
