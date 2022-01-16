<?php
/**
 *   __       _           _
 *  / _| __ _| |__  _   _| | ___   __ _
 * | |_ / _` | '_ \| | | | |/ _ \ / _` |
 * |  _| (_| | |_) | |_| | | (_) | (_| |
 * |_|  \__,_|_.__/ \__,_|_|\___/ \__, |
 *                                 |___/
 *
 *  fabulog - your fabulous blogware
 *
 *  Copyright (c) 2017 by ikkez
 *  Christian Knuth <mail@ikkez.de>
 *  https://github.com/ikkez/fabulog/
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 */

function module_config(Base $f3)
{
    $f3->set('APP_VERSION', '0.3.1');
    $f3->BITMASK = ENT_COMPAT|ENT_SUBSTITUTE;

//ini_set('display_errors', 1);
//error_reporting(-1);

    $f3->config('blog/config.ini');

    $mydir = Loader::endslash($f3->get('MODULE'));

    // preflight system check
    $preErr = Setup::instance()->preflight();
    if (!empty($preErr)) {
            header('Content-Type: text;');
            die(implode("\n",$preErr));
    }
    
    ## DB Setup
    $cfg = Config::instance();
    $db = $cfg->ACTIVE_DB;
    
    if ($db) {
        $f3->set('DB', Storage::instance()->get($db));
    }
    else {
        $f3->error(500,'Sorry, but there is no active DB setup.');
    }

    $f3->set('CONFIG', $cfg);
    $f3->set('FLASH', \Flash::instance());

    $tmpl = \Template::instance();
    $tmpl->extend('image','\Template\Tags\Image::render');
    $tmpl->extend('pagebrowser','\Pagination::renderTag');
    // Handles all <form> data
    $tmpl->extend('input','\Template\Tags\Input::render');
    $tmpl->extend('select','\Template\Tags\Select::render');
    \Assets::instance();

    $f3->config($mydir . 'routes.ini');

    ///////////////
    //  backend  //
    ///////////////

    if (\Controller\Auth::isLoggedIn()) {
        $f3->config($mydir . 'routes_admin.ini');
    }
 
}
/** @var \Base $f3 */
$f3 = \Base::instance();
module_config($f3);

$f3->run();
