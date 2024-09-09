<?php

use Authorization\Authorization;

function anyChildPermission($tree)
{
    foreach ($tree as $element) {
        if (!empty($element->permission->group) && !empty($element->permission->name)) {
            if (Authorization::getUserData()->permissions->can($element->permission->group, $element->permission->name))
                return true;
        } else {
            return true;
        }
    }
    return false;
}

function generateMenu($tree)
{
    echo '<ul>';
    foreach ($tree as $element) {
        if (!empty($element->permission->group) && !empty($element->permission->name)&&!empty(Authorization::getUserData())) {
            if (!Authorization::getUserData()->permissions->can($element->permission->group, $element->permission->name))
                continue;
        } else if (!isset($element->link) && isset($element->menu)) {
            if (!anyChildPermission($element->menu))
                continue;
        }
        echo '<li>';
        if (isset($element->link))
            echo '<a href="' . htmlspecialchars($element->link) . '">' . htmlspecialchars($element->title??'') . '</a>';
        else
            echo '<span>' . htmlspecialchars($element->title??'') . '</span>';
        if (isset($element->menu))
            generateMenu($element->menu);
        echo '</li>';
    }
    echo '</ul>';
}

?>
<nav>
    <?= generateMenu($data['menu']); ?>
</nav>
<section class="madeBy">Made by <a href="https://green-code.studio.">Green Code Studio</a></section>
