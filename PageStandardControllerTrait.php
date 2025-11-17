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
        $userPreferences = $userData?->preferences ?? [];
        $userPreferences = [...$userPreferences, ...($_GET['_preferences']??[])];
        $menu = new Menu();
        $menuData = $menu->readMenu();
        $notifications = new Notifications();
        $notificationsList = $notifications->getForCurrentUser();
        $this->addView('CommonBase', 'aside', ['menu' => $menuData], 'aside');
        $header = $this->getPageHeader();
        require __DIR__.'/../CommonBase/Views/template.php';
    }

    public function getPageHeader()
    {
        return ['title' => 'System'];
    }

    public function hasPermission(string $methodName)
    {
        return Authorization::isLogged();
    }

    public function getInitInfo()
    {
        if (Authorization::isLogged()) {
            $this->initInfo->permissions = \Authorization\Authorization::getUserData()->permissions->getAsArray();
        }
        return $this->initInfo;
    }

}
