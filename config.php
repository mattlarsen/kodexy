<?php

/*======================================
Kodexy Framework v0.8.1
Author: Matt Larsen
Web: perthcomputing.com/projects/kodexy
======================================*/

/**
 *	Kodexy configuration file.
 */

return array(
	'baseUrl'					=> 'http://localhost/kodexy/', //base website URL. Includes trailing forward slash.
	'title'						=> 'Kodexy', //default page title
	'development'				=> TRUE, //show PHP errors and dev messages?
	'database'					=> array(
		'autoConnect'			=> FALSE,
		'dsn'					=> 'mysql:host=localhost;dbname=mydb;charset=utf8', //PDO DSN. e.g. mysql:host=localhost;dbname=someDatabase;charset=utf8
		'username'				=> 'root',
		'password'				=> '',
		'driverOptions'			=> array(),
	),
	'autoFilterXss'				=> TRUE, //automatically filter variables passed to views for XSS? See also unxss.
	//custom config options...
);

ini_set('session.gc_probability', '1');
ini_set('session.gc_divisor', '100');
ini_set('session.gc_maxlifetime', '1800');