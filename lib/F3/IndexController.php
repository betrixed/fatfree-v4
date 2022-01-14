<?php

namespace F3;

use WC\BaseController;
use Base,
    View;

class IndexController extends BaseController {

    public function index(Base $f3)
    {
        $this->prefixUI("lib/F3");

        $classes = [
        'Base' => [
        'hash',
        'json',
        'session',
        'mbstring'
        ],
        'Cache' => [
        'apc',
        'apcu',
        'memcache',
        'memcached',
        'redis',
        'wincache',
        'xcache'
        ],
        'DB\SQL' => [
        'pdo',
        'pdo_dblib',
        'pdo_mssql',
        'pdo_mysql',
        'pdo_odbc',
        'pdo_pgsql',
        'pdo_sqlite',
        'pdo_sqlsrv'
        ],
        'DB\Jig' => ['json'],
        'DB\Mongo' => ['json', 'mongo'],
        'Auth' => ['ldap', 'pdo'],
        'Bcrypt' => [ 'openssl'],
        'Image' => ['gd'],
        'Lexicon' => ['iconv'],
        'SMTP' => ['openssl'],
        'Web' => ['curl', 'openssl', 'simplexml'],
        'Web\Geo' => ['geoip', 'json'],
        'Web\OpenID' => ['json', 'simplexml'],
        'Web\OAuth2' =>['json'],
        'Web\Pingback' => ['dom', 'xmlrpc'],
        'CLI\WS' => ['pcntl']
        ];

        $f3->render_time = microtime(true);
        $view = View::instance();

        echo $view->render('layout.phtml', [
            'content' =>
            $view->render('welcome.phtml', ['classes' => $classes])
        ]);
    }

}
