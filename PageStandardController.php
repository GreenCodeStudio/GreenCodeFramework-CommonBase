<?php

namespace Common;

class PageStandardController extends \Core\StandardController {
    public function postAction() {
        $this->addView('Common', 'aside',null, 'aside');
        require __DIR__.'/../Common/Views/template.php';
    }
}
