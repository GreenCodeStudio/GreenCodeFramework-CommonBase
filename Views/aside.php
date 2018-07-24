<?php
function generateMenu($tree)
{
    echo '<ul>';
    foreach ($tree as $element) {
        echo '<li>';
        if (isset($element->link))
            echo '<a href="'.htmlspecialchars($element->link).'">'.htmlspecialchars($element->title).'</a>';
        else
            echo '<span>'.htmlspecialchars($element->title).'</span>';
        if (isset($element->menu))
            generateMenu($element->menu);
        echo '</li>';
    }
    echo '</ul>';
} ?>
<nav>
    <?= generateMenu($data['menu']); ?>
</nav>