<?php
/**
 * Created by PhpStorm.
 * User: matri
 * Date: 24.07.2018
 * Time: 11:56
 */

namespace CommonBase\Controllers;


use Common\PageStandardController;
use Core\CodeCache;

class Cache extends PageStandardController
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
        CodeCache::regenerate();
    }
}