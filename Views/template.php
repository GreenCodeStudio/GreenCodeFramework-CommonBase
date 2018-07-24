<!DOCTYPE html>
<html class="color-green">
<head>
    <title>TODO supply a title</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="/css/build.css" rel="stylesheet" type="text/css">
    <link rel="manifest" href="/manifest.json">
</head>
<body>
<header>
    <div class="tasks"></div>
</header>
<aside data-views="aside"><?php $this->showViews('aside'); ?></aside>
<div class="mainContent">
    <div class="topBar">
        <div class="breadcrumb"><?php $this->showBreadcrumb(); ?></div>
    </div>
    <div data-views="main"><?php $this->showViews('main'); ?></div>
</div>
<script>
    window.controllerInitInfo = <?=json_encode($this->getInitInfo())?>;
</script>
<script src="/js/main.bundle.js" type="text/javascript"></script>
</body>
</html>
