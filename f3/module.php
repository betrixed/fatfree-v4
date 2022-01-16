<?php


function mod_config(Base $f3) : void {
    
    $f3->config('f3/config.ini');
    
    $f3->route('GET /userref',
        function($f3) {
                $view = View::instance();
                
                $f3->set('content', $view->render('userref.phtml'));
                echo $view->render('layout.phtml');
        }
        );

}

try {
    mod_config($f3);
    $f3->run();
}
catch(Exception $ex) {
    echo $ex->getMessage() . PHP_EOL;
}
