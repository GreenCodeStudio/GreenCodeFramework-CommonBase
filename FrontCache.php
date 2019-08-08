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
        $ret = ['/cache/offline'];
        $js = scandir(__DIR__.'/../../public_html/js');
        foreach ($js as $jsFile) {
            if (substr($jsFile, 0, 1) != '.')
                $ret[] = '/js/'.$jsFile;
        }
        $css = scandir(__DIR__.'/../../public_html/css');
        foreach ($css as $cssFile) {
            if (substr($cssFile, 0, 1) != '.')
                $ret[] = '/css/'.$cssFile;
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