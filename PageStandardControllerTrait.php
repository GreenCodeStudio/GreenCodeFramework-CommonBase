<?php

namespace CommonBase;

trait PageStandardControllerTrait
{
    public function postAction()
    {
        $userData = \Authorization\Authorization::getUserData();
        $menu = new \Core\Menu();
        $menuData = $menu->readMenu();
        $this->addView('CommonBase', 'aside', ['menu' => $menuData], 'aside');
        require __DIR__.'/../CommonBase/Views/template.php';
    }
    public function hasPermission()
    {
        return \Authorization\Authorization::isLogged();
    }
}
