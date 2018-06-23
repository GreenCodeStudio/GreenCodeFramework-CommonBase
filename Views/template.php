<!DOCTYPE html>
<html>
    <head>
        <title>TODO supply a title</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="/css/build.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        <header></header>
        <aside data-views="aside"><?php $this->showViews('aside'); ?></aside>
        <div class="mainContent" data-views="main"><?php $this->showViews('main'); ?></div>
        <script src="/js/main.bundle.js" type="text/javascript"></script>
    </body>
</html>
