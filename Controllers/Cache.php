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
    /**
     * @OfflineConstant
     */
    function offline()
    {
        $this->addView('CommonBase', 'offline');
    }

    function regenerateCode()
    {
        \Core\CodeCache::regenerate();
    }
}