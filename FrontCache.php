<?php
/**
 * Created by PhpStorm.
 * User: matri
 * Date: 24.07.2018
 * Time: 11:58
 */

namespace CommonBase;


class FrontCache
{
    public function getNormalList()
    {
        $ret = ['/Cache/offline'];
        $dist = scandir(__DIR__.'/../../public_html/dist');
        foreach ($dist as $file) {
            if (substr($file, 0, 1) != '.')
                $ret[] = '/dist/'.$file;
        }
        return $ret;
    }

    public function getJsonList()
    {
        $ret = [];
        $controllers = \Core\Router::listControllers('Controllers');
        foreach ($controllers as $controller) {
            foreach ($controller->methods as $method) {
                $constant = false;
                foreach ($method->annotations as $annotation) {
                    if (is_a($annotation, OfflineConstantAnnotation))
                        $constant = true;
                }
                if ($constant)
                    $ret[] = "/$controller->name/$method->name";
            }
        }
        return $ret;
    }
}