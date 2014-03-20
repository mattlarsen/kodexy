<?php

/*======================================
Kodexy Framework v0.8.1
Author: Matt Larsen
Web: perthcomputing.com/projects/kodexy
======================================*/

//path to application, e.g. "c:/wamp/www/".
if(!defined('APP_PATH'))
{
	define('APP_PATH', str_replace('\\', '/', dirname(dirname(__FILE__))).'/');
}

//load system files
require(APP_PATH.'system/utilities.php');
require(APP_PATH.'system/Router.php');
require(APP_PATH.'system/Database.php');
require(APP_PATH.'system/FormHandler.php');
require(APP_PATH.'system/Session.php');

/**
 * Core of the Kodexy framework.
 */
class Kodexy
{
	public static $config; //from config.php
	public static $pageTitle;
	public static $pageHead;
	
	/**
	 * Loads components and routes the request.
	 */
	public static function bootstrap()
	{
		self::$config = require(APP_PATH.'config.php'); //get config
		define('BASE_URL', self::$config['baseUrl']);
		
		self::$pageTitle = self::$config['title'];
		self::$pageHead = '';
		
		//error reporting
		error_reporting(E_ALL|E_STRICT);
		if(!self::$config['development'])
		{
			//log, but don't display PHP errors.
			ini_set('display_errors', 0);
			ini_set('log_errors', 1);
		}
		
		//connect to database
		if(self::$config['database']['autoConnect'])
		{
			Database::connect();
		}
		
		//startup
		require(APP_PATH.'startup.php');
		
		//process request
		Router::route();
	}
	
	/**
	 * Adds a message to be displayed on the next page load. Useful for form submissions.
	 */
	public static function addMessage($message)
	{
		$_SESSION['kodexy']['messages'][] = $message;
	}
	
	/**
	 * Adds an error message to be displayed on the next page load.
	 */
	public static function addErrorMessage($message)
	{
		$_SESSION['kodexy']['errorMessages'][] = $message;
	}
	
	/**
	 * Returns whether or not there are messages to be displayed.
	 */
	public static function isMessages()
	{
		return (count($_SESSION['kodexy']['messages']) > 0);
	}
	
	/**
	 * Returns whether or not there are error messages to be displayed.
	 */
	public static function isErrorMessages()
	{
		return (count($_SESSION['kodexy']['errorMessages']) > 0);
	}
	
	/**
	 * Returns an array of session messages.
	 */
	public static function getMessages()
	{
		$messages = $_SESSION['kodexy']['messages'];
		$_SESSION['kodexy']['messages'] = array();
		return $messages;
	}
	
	/**
	 * Returns an array of session error messages.
	 */
	public static function getErrorMessages()
	{
		$messages = $_SESSION['kodexy']['errorMessages'];
		$_SESSION['kodexy']['errorMessages'] = array();
		return $messages;
	}
	
	/**
	 * Render output.
	 * @param $view - view file located within "views". ".php" not necessary.
	 * @param $data - associative array of variables to pass to the view. Keys = variable names, values = values.
	 */
	public static function loadView($view, $data = array())
	{
		if(Kodexy::$config['autoFilterXss'])
		{
			//security: auto filter for xss.
			$data = xss(unxss($data));
		}
		
		//output view
		extract($data);
		require(APP_PATH.'views/'.$view.'.php');
	}

	/**
	 * Load code.
	 * @param $code - name of the file to load from the "code" folder. ".php" not necessary.
	 */
	public static function loadCode($code)
	{
		$file = APP_PATH.'code/'.$code.'.php';
		$newDir = dirname($file);
		$curDir = getcwd();
		
		chdir($newDir);
		require($file);
		chdir($curDir);
	}
}