<?php

namespace CommonBase\Controllers;


use Common\PageStandardController;

class Error extends PageStandardController
{
    function index(int $responseCode, $debugOutput)
    {
        if($responseCode==403)
            $this->addView('CommonBase', '403');
        else if($responseCode==404)
            $this->addView('CommonBase', '404');
        else if($responseCode==500)
            $this->addView('CommonBase', '500');
        else if(!empty($debugOutput))
          $this->addViewString($debugOutput);
    }
}