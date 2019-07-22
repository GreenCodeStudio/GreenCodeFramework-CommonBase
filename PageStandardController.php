<?php

namespace Common;

class PageStandardController extends \Core\StandardController
{
    public function postAction()
    {
        $userData = \Authorization\Authorization::getUserData();
        $menu = new \Core\Menu();
        $menuData = $menu->readMenu();
        $this->addView('Common', 'aside', ['menu' => $menuData], 'aside');
        require __DIR__.'/../Common/Views/template.php';
    }
    public function hasPermission()
    {
        return \Authorization\Authorization::isLogged();
    }
}
