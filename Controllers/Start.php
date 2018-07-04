<?php

namespace Common\Controllers;

class Start extends \Common\PageStandardController {

    function index() {
        $this->addView('Common', 'start');
        dumpTime();
        dumpTime();
        dump(\Core\DB::get("SELECT 1"));
        dumpTime();
//        $t1= microtime(true);
//            $long='jfnsndndksamkm';
//            for($j=0;$j<10;$j++){
//                $long=$long.$long;
//            }
//        for($i=0;$i<1000;$i++){
//            $uniq= uniqid();
//            file_get_contents('C:\Users\matri\AppData\Local\Temp\Nowy folder\\'.$i.'.txt');            
//is_file('C:\Users\matri\AppData\Local\Temp\Nowy folder\\'.$uniq.'.txt');
//            //file_put_contents('C:\Users\matri\AppData\Local\Temp\Nowy folder\\'.$uniq.'.txt', $uniq, FILE_APPEND);
//            //file_put_contents('C:\Users\matri\AppData\Local\Temp\Nowy folder\\'.$i.'.txt', $long, FILE_APPEND);
//        }
    }

    function demo($a, int $b = 5, \Exception $c = null) {
        dump($a, $b);
    }
    function migration(){
        $migr=new \Core\Migration();
        $migr->upgrade();
    }
    function formDemo(){
        $this->addView('Common', 'formDemo');
    }
    function formDemo_data(){
        return ['demo'=>['test1'=>rand(10,20)]];
    }

}
