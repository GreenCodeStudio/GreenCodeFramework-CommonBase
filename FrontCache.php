<?php

namespace CommonBase;

use Core\Router;

include_once(__DIR__.'/../Core/Annotations.php');

class FrontCache
{
    public function getNormalList()
    {
        $consts = ['/Cache/offline'];
        return array_merge($consts, $this->getFilesRecurse('/dist'));
    }

    private function getFilesRecurse($path)
    {
        $ret = [];
        $prefix = __DIR__.'/../../public_html';
        $files = scandir($prefix.$path);
        foreach ($files as $file) {
            $full = "$path/$file";
            if (substr($file, 0, 1) != '.') {
                if (is_dir($file)) {
                    $ret = array_merge($ret, $this->getFilesRecurse($full));
                } else {
                    $ret[] = $full;
                }
            }
        }
        return $ret;
    }

    public function getJsonList()
    {
        $ret = [];
        $controllers = Router::listControllers('Controllers');
        foreach ($controllers as $controller) {
            foreach ($controller->methods as $method) {
                $constant = false;
                foreach ($method->annotations as $annotation) {
                    if (is_a($annotation, "OfflineConstantAnnotation"))
                        $constant = true;
                }
                if ($constant)
                    $ret[] = "/$controller->name/$method->name";
            }
        }
        return $ret;
    }
}