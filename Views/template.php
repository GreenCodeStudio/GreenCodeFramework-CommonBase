<?php

use Common\Formatter;

?>
<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($this->getTitle()) ?></title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="/dist/main.css" rel="stylesheet" type="text/css">
    <link rel="manifest" href="/dist/Common/manifest.json">
    <link rel="shortcut icon" href="/dist/Common/icon.png">
    <link rel="icon" sizes="192x192" href="/dist/Common/icon192.png">
    <meta name="theme-color" content="#d7ee1b">
</head>
<body>
<header>
    <button type="button" class="hamburgerMenu">menu</button>
    <h1><?= htmlspecialchars($header['title']) ?></h1>
        <div class="mainSearch">
            <input type="search" placeholder="Szukaj">
            <div class="list"></div>
        </div>

    <div class="tasks">
        <div class="headerButton"><span class="icon-tasks"></span></div>
        <div class="tasksList"></div>
    </div>
    <div class="notifications">
        <div class="headerButton"><span class="icon-notifications"></span></div>
        <div class="notificationsList">
            <button class="subscribe-notifications">Włącz powiadomienia push</button>
            <?php foreach ($notificationsList as $notification) {
                ?>
                <div class="notification">
                    <div class="date"><?= Formatter::formatDate($notification->stamp) ?></div>
                    <div class="title"><?= htmlspecialchars($notification->message) ?></div>
                </div>
                <?php
            } ?>
        </div>
    </div>
    <div class="loginInfo">
        <div class="headerButton"><span class="icon-user"></span></div>
        <div class="loginInfo-expandable">
            <span class="icon-user"><?= htmlspecialchars($userData->name.' '.$userData->surname) ?></span>
            <div class="button logoutMyselfBtn" title="Wyloguj"><span class="icon-logout"></span>Wyloguj</div>
            <button type="button" onclick="document.documentElement.classList.toggle('win98')">Wygląd klasyczny</button>
        </div>
    </div>
</header>
<aside data-views="aside"><?php $this->showViews('aside'); ?></aside>
<div class="mainContent">
    <div class="topBar">
        <div class="breadcrumb"><?php $this->showBreadcrumb(); ?></div>
    </div>
    <div data-views="main"><?php $this->showViews('main'); ?></div>
</div>
<script>
    //<![CDATA[
    window.controllerInitInfo = <?=json_encode($this->getInitInfo())?>;
    window.DEBUG =<?=json_encode($this->isDebug())?>;
    /]]>
</script>
<script src="/dist/main.js" type="text/javascript"></script>
</body>
</html>
