<?php
/**
 * Created by PhpStorm.
 * User: matri
 * Date: 24.07.2018
 * Time: 11:56
 */

namespace CommonBase\Controllers;


class Cache extends \Common\PageStandardController
{
    function offline()
    {

    }

    function regenerateCode()
    {
        \Core\CodeCache::regenerate();
    }
}