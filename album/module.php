<?php

namespace WC;
Use DB\SQL;
use Base;

$f3->route('GET /album', 'Album\Controller->indexGet');

function mod_config(Base $f3) 
{
    $f3->show_time = true;
    $f3->config('album/config.ini');
    $services = Services::instance();
    $services->setShared('db',function($sql) use ($f3) {
        return new SQL($f3->get('DB'));
    });
    $services->setShared('f3',$f3);
}

try {
    mod_config($f3);
    $f3->run();
}
catch(Exception $ex) {
    echo $ex->getMessage() . PHP_EOL;
}


