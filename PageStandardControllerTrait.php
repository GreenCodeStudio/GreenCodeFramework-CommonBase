<?php

namespace CommonBase;

use Authorization\Authorization;
use Core\Menu;
use Notifications\Notifications;

trait PageStandardControllerTrait
{
    public function postAction()
    {
        $userData = Authorization::getUserData();
        $menu = new Menu();
        $menuData = $menu->readMenu();
        $notifications = new Notifications();
        $notificationsList = $notifications->getForCurrentUser();
        $this->addView('CommonBase', 'aside', ['menu' => $menuData], 'aside');
        require __DIR__.'/../CommonBase/Views/template.php';
    }

    public function hasPermission()
    {
        return Authorization::isLogged();
    }
}
