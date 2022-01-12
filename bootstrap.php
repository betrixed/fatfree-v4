<?php


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
error_log(__DIR__."/log/errors.txt");

// Kickstart the framework
use DB\SQL;
use WC\{WConfig, Services};

chdir(__DIR__);

$f3=require('lib/base.php');

$f3->set('DEBUG',1);
if ((float)PCRE_VERSION<8.0)
	trigger_error('PCRE version is out of date');

// Load configuration
$f3->config('config.ini');


$gApp = new WConfig();
$gApp->show_time = true;

new Services($gApp);

function setup_album_services(Base $f3) {
    $services = Services::instance();
    
    $services->setShared('db',function($sql) use ($f3) {
        return new SQL($f3->get('DB'));
    });
    
    $f3->route('GET /album', 'Album\Controller->indexGet');
}
    
setup_album_services($f3);



// A welcome page function / factory
$welcome_page = function($f3) {
    global $gApp;
    
		$classes=array(
			'Base'=>
				array(
					'hash',
					'json',
					'session',
					'mbstring'
				),
			'Cache'=>
				array(
					'apc',
					'apcu',
					'memcache',
					'memcached',
					'redis',
					'wincache',
					'xcache'
				),
			'DB\SQL'=>
				array(
					'pdo',
					'pdo_dblib',
					'pdo_mssql',
					'pdo_mysql',
					'pdo_odbc',
					'pdo_pgsql',
					'pdo_sqlite',
					'pdo_sqlsrv'
				),
			'DB\Jig'=>
				array('json'),
			'DB\Mongo'=>
				array(
					'json',
					'mongo'
				),
			'Auth'=>
				array('ldap','pdo'),
			'Bcrypt'=>
				array(
					'openssl'
				),
			'Image'=>
				array('gd'),
			'Lexicon'=>
				array('iconv'),
			'SMTP'=>
				array('openssl'),
			'Web'=>
				array('curl','openssl','simplexml'),
			'Web\Geo'=>
				array('geoip','json'),
			'Web\OpenID'=>
				array('json','simplexml'),
			'Web\OAuth2'=>
				array('json'),
			'Web\Pingback'=>
				array('dom','xmlrpc'),
			'CLI\WS'=>
				array('pcntl')
		);

                $gApp->render_time = microtime(true);
                $view = View::instance();
                
                echo $view->render('layout.phtml', [
                    'content' => 
                     $view->render('welcome.phtml',['classes' => $classes])
                ]);

	};

$f3->route('GET /', $welcome_page);
$f3->route('GET /index.php', $welcome_page);

$f3->route('GET /userref',
	function($f3) {
		$f3->set('content','userref.phtml');
		echo View::instance()->render('layout.phtml');
	}
);

$f3->run();
