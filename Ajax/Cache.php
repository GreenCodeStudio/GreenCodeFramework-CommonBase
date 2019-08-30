<?php
/**
 * Created by PhpStorm.
 * User: matri
 * Date: 24.07.2018
 * Time: 11:57
 */

namespace CommonBase\Ajax;


use Common\PageAjaxController;
use CommonBase\FrontCache;

class Cache extends PageAjaxController
{
    public function list()
    {
        $frontCache = new FrontCache();
        $normal = $frontCache->getNormalList();
        $json = $frontCache->getJsonList();

        return ['normal' => $normal, 'json' => $json];
    }
}