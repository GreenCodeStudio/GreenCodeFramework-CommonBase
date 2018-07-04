<?php

namespace Common;

class PageStandardController extends \Core\StandardController
{
    public function postAction()
    {
        $menu = new \core\Menu();
        $menuData = $menu->readMenu();
        $this->addView('Common', 'aside', ['menu' => $menuData], 'aside');
        require __DIR__.'/../Common/Views/template.php';
    }
}
