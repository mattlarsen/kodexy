<?php

/*======================================
Kodexy Framework v0.8.1
Author: Matt Larsen
Web: perthcomputing.com/projects/kodexy
======================================*/

/**
 * Manages user sessions.
 */
class Session
{
	/**
	 * Initializes session management for the request. To be called on startup.
	 */
	public static function init()
	{
		//start session
		session_start();
		
		//create session if one doesn't yet exist
		if(!isset($_SESSION['kodexy']))
		{
			self::createSession();
		}
		
		//security
		self::updateSession();
	}
	
	/**
	 * Create a new session.
	 */
	public static function createSession()
	{
		$_SESSION['kodexy'] = array(
			'csrf'				=> randomString(32),
			'messages'			=> array(),
			'errorMessages'		=> array(),
			'lastActivity'		=> time(),
			'created'			=> time(),
			'logged'			=> FALSE,
		);
	}
	
	/**
	 * Securely unset session.
	 */
	public static function destroySession()
	{
		$_SESSION = array();
		session_destroy();
		
		session_start(); //restart, so sessions can still be used.
	}
	
	/**
	 * Security housekeeping.
	 */
	public static function updateSession()
	{
		$_SESSION['kodexy']['lastActivity'] = time();
		
		if(time() - $_SESSION['kodexy']['created'] > 1800) //session started more than 30 minutes ago
		{
			self::regenerateSessionId(); //refresh session ID
		}
	}
	
	/**
	 * Move to new session for security purposes.
	 */
	public static function regenerateSessionId()
	{
		//get data
		$sessionData = $_SESSION;
		
		//destroy user session
		self::destroySession();
		
		//set data
		$_SESSION = $sessionData;
		$_SESSION['kodexy']['created'] = time();
	}
	
	/**
	 * Returns the CSRF token for the session.
	 */
	public static function getCsrfToken()
	{
		return $_SESSION['kodexy']['csrf'];
	}
	
	/**
	 * Validates the CSRF token for the session.
	 */
	public static function validateCsrfToken($token)
	{
		if($token != $_SESSION['kodexy']['csrf'])
		{
			error_log('Security exception: invalid CSRF token. IP: '.$_SERVER['REMOTE_ADDR']);
			
			if(Kodexy::$config['development'])
			{
				die('Security exception: invalid CSRF token.');
			}
			else
			{
				die('Security exception.');
			}
		}
	}
	
	/**
	 * Logs a user into the system.
	 */
	public static function login()
	{
		self::destroySession();
		self::createSession();
		$_SESSION['kodexy']['logged'] = TRUE;
	}
	
	/**
	 * Returns whether or not the user is logged into the system.
	 */
	public static function isLoggedIn()
	{
		return (bool)$_SESSION['kodexy']['logged'];
	}
	
	/**
	 * Logs out a user from the system.
	 */
	public static function logout()
	{
		self::destroySession();
		self::createSession();
	}
}