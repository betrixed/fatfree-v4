<?php


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

error_reporting(E_ALL);
error_log(__DIR__."/log/errors.txt");

// Kickstart the framework
use DB\SQL;
use WC\Services;

chdir(__DIR__);

$f3=require('lib/base.php');
$f3->set('DEBUG',1);

// Load configuration
$f3->config('config.ini');


//Cache::instance()->load('memcached=localhost:11211');


if ((float)PCRE_VERSION<8.0)
	trigger_error('PCRE version is out of date');

function setup_services(Base $f3) {
    
    /* F3 does not mind using Base object
      properties as another data namespace.
    */
    
    $f3->show_time = true;
    
    $services = Services::instance();
    
    $services->setShared('db',function($sql) use ($f3) {
        return new SQL($f3->get('DB'));
    });
    
    $services->setShared('f3', $f3);

}
    
setup_services($f3);


$f3->route('GET /album', 'Album\Controller->indexGet');

$f3->route('GET /userref',
	function($f3) {
                $view = View::instance();
                $f3->ref('VIEW_INJECT')[] = 'content';
		$f3->set('content', $view->render('userref.phtml'));
		echo $view->render('layout.phtml');
	}
);

try {
$f3->run();
}
catch(Exception $ex) {
    echo $ex->getMessage() . PHP_EOL;
}
