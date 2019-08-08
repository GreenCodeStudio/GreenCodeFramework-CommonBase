<?php
/**
 * Created by PhpStorm.
 * User: matri
 * Date: 24.07.2018
 * Time: 11:57
 */

namespace CommonBase\Ajax;


class Cache extends \Common\PageAjaxController
{
    public function list()
    {
        $frontCache = new \CommonBase\FrontCache();
        $normal = $frontCache->getNormalList();
        $json = $frontCache->getJsonList();

        return ['normal' => $normal, 'json' => $json];
    }
}