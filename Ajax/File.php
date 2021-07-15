<?php


namespace CommonBase\Ajax;


use Common\PageAjaxController;

class File extends PageAjaxController
{
    public function upload($file, $other)
    {
        return (new \File\File())->upload($file);
    }

}