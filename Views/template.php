<?php

use Core\Formatter;

?>
<!DOCTYPE html>
<html data-layout="<?=($userData?->preferences["CommonBase.layout"])??'metro'?>">
<head>
    <title><?= htmlspecialchars($this->getTitle()) ?></title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="/dist/main.css" rel="stylesheet" type="text/css">
    <link rel="manifest" href="/dist/Common/manifest.json">
    <link rel="shortcut icon" href="/dist/Common/icon.png">
    <link rel="icon" sizes="192x192" href="/dist/Common/icon192.png">
    <meta name="theme-color" content="#d7ee1b">
    <?php foreach ($this->getHeadLinks() ?? [] as $link){
        echo '<link ';
        foreach ($link as $key=>$value){
            echo htmlspecialchars($key).'="'.htmlspecialchars($value).'" ';
        }
        echo '>';
    } ?>

    <?= $this->headerHtml() ?>
</head>
<body>
<header>
    <button type="button" class="hamburgerMenu"><span class="icon-menu"></span></button>
    <h1><?= htmlspecialchars($header['title']) ?></h1>
        <div class="mainSearch">
            <input type="search" placeholder="Szukaj">
            <div class="list"></div>
        </div>
    <div class="loginInfo">
        <a class="headerButton" href="/User/myAccount"><span class="icon-user"></span></a>
        <?php if(!empty($userData)){?>
        <div class="loginInfo-expandable">
            <span class="icon-user"><?= htmlspecialchars($userData->name.' '.$userData->surname) ?></span>
            <a href="/User/myAccount" class="button">Moje konto</a>
            <button class="subscribe-notifications">Włącz powiadomienia push</button>
            <div class="button logoutMyselfBtn" title="Wyloguj"><span class="icon-logout"></span>Wyloguj</div>
<!--            <button type="button" onclick="document.documentElement.classList.toggle('win98')">Wygląd klasyczny</button>-->
        </div>
        <?php } ?>
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
    window.initNotifications=<?=json_encode($notificationsList)?>;
    window.webSocketPort = <?=(int)$_ENV['websocketPort']?>;
    window.firebaseSenderId = <?=json_encode($_ENV['firebase_sender_id'])?>;
    //]]>
</script>
<script src="/dist/main.js" type="text/javascript"></script>
</body>
</html>
