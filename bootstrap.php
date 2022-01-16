<?php



ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

error_reporting(E_ALL);
error_log(__DIR__."/log/errors.txt");

// Kickstart the framework

chdir(__DIR__);

$f3=require('lib/base.php');


function get_module(Base $f3) : ?string
{
    $modules = [
        'album' => 'album', 
        'f3' => 'f3', 
        'blog' => 'blog',
        'default' => 'f3'
    ];
    $uri = $f3->get('URI');
    $match = null;
    if (preg_match('/\/([\w]+)(?:\.php)?\/?/iu', $uri, $match)) {
        $mod = strtolower($match[1]);
        $path = $modules[$mod] ?? null;
        if ($path) {
            return $path;
        }
    }
    $f3->show_time = true;
    return $modules['default'];
}

require get_module($f3) . "/module.php";


