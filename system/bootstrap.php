<?php

/*======================================
Kodexy Framework v0.8.2
Author: Matt Larsen
Web: github.com/mattlarsen/kodexy
======================================*/

/**
 * Kodexy bootstrap procedural file.
 */

//path to application, e.g. "c:/wamp/www/".
if (!defined('APP_PATH'))
{
    define('APP_PATH', str_replace('\\', '/', dirname(dirname(__FILE__))).'/');
}

//load system files
require(APP_PATH.'system/utilities.php');
require(APP_PATH.'system/Kodexy.php');
require(APP_PATH.'system/Router.php');
require(APP_PATH.'system/Database.php');
require(APP_PATH.'system/FormHandler.php');
require(APP_PATH.'system/Session.php');

//begin request
$kodexy = new Kodexy\Kodexy();
$kodexy->bootstrap();