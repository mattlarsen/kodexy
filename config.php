<?php

/*======================================
Kodexy Framework v0.8.2
Author: Matt Larsen
Web: github.com/mattlarsen/kodexy
======================================*/

/**
 * Kodexy configuration file.
 */

return array(
    'baseUrl'                    => 'http://localhost/kodexy/', //base website URL. Includes trailing forward slash.
    'pageTitle'                  => 'Kodexy', //default page title
    'displayErrors'              => true, //show PHP errors and dev messages?
    
    'dbAutoConnect'              => false,
    'dbDsn'                      => 'mysql:host=localhost;dbname=mydb;charset=utf8', //PDO DSN. e.g. mysql:host=localhost;dbname=someDatabase;charset=utf8
    'dbUsername'                 => 'root',
    'dbPassword'                 => '',
    'dbDriverOptions'            => array(),
    
    'autoFilterXss'              => true, //automatically filter variables passed to views for XSS? See also unxss.
    
    //custom config options...
);