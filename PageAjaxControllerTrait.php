<?php

namespace CommonBase;

use Authorization\Authorization;

trait PageAjaxControllerTrait
{

    public function hasPermission(string $methodName)
    {
        return Authorization::isLogged();
    }
}
