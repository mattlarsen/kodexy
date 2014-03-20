<?php

/*======================================
Kodexy Framework v0.8.1
Author: Matt Larsen
Web: perthcomputing.com/projects/kodexy
======================================*/

/**
 * Routes requests.
 */
class Router
{
	private static $request;
	private static $params;
	
	/**
	 * Gets the request (controller and parameters as specified in the URL).
	 */
	public static function getRequest()
	{
		return self::$request;
	}
	
	/**
	 * Gets the parameters for the request (URL segments after the controller).
	 * @param $index - 0-based index of the parameter.
	 * @param $defaultValue - to return if the parameter doesn't exist
	 */
	public static function getParam($index, $defaultValue = NULL)
	{
		if(isset(self::$params[$index]))
		{
			return self::$params[$index];
		}
		
		return $defaultValue;
	}
	
	/**
	 * Routes the request to the associated controller.
	 */
	public static function route()
	{
		$request = '';
		if(isset($_GET['q']))
		{
			$request = $_GET['q'];
		}
		
		self::$request = $request;
		self::rebuildGet();
		
		//get controller
		$found = FALSE;
		
		if($request == '')
		{
			$request = 'index';
		}
		
		$segments = explode('/', $request);
		$dir = APP_PATH.'controllers/';
		foreach($segments as $index => $segment)
		{
			$segment = str_replace('-', '_', $segment);
			if(file_exists($dir.$segment.'.php'))
			{
				//load controller
				$found = TRUE;
				self::$params = array_slice($segments, $index+1);
				require($dir.$segment.'.php');
				break;
			}
			else
			{
				$dir .= $segment.'/';
			}
		}
		
		if(!$found)
		{
			//try index controller
			if(file_exists($dir.'index.php'))
			{
				//load controller
				$found = TRUE;
				self::$params = array();
				require($dir.'index.php');
			}
		}
		
		if(!$found)
		{
			self::notFound();
		}
		
		self::completeRequest();
	}
	
	/**
	 * Performs final closing-down tasks.
	 */
	public static function completeRequest()
	{
		if(Database::isConnected())
		{
			Database::disconnect();
		}
		
		exit;
	}
	
	/**
	 * Shows a 404 Not Found page.
	 */
	public static function notFound()
	{
		http_response_code(404);
		error_log('404 page not found: '.self::getRequest());
		Kodexy::loadView('system/notFound');
		self::completeRequest();
	}
	
	/**
	 * Redirects the browser to another URL.
	 */
	public static function redirect($url)
	{
		if(strpos($url, '://') === FALSE) //relative URL
		{
			$url = BASE_URL.$url;
		}
		
		header('Location: '.$url); //redirect
		exit;
	}
	
	/**
	 * Displays the specified file inline.
	 * @param $filename - filename extending from the application directory (e.g. "uploads/A7shG.jpg").
	 */
	public static function displayFile($filename, $mime)
	{
		header('Content-Type: '.$mime);
		header('Content-Length: '.filesize(APP_PATH.$filename));
		header('Content-Disposition: inline; filename='.basename($filename));
		
		readfile(APP_PATH.$filename);
		
		self::completeRequest();
	}
	
	/**
	 * Forces the client's browser to download the file specified.
	 * @param $filename - filename extending from the application directory (e.g. "uploads/uISH5.docx").
	 */
	public static function forceDownload($filename, $mime = 'application/octet-stream')
	{
		header('Content-Description: File Transfer');
		header('Content-Type: '.$mime);
		header('Content-Disposition: attachment; filename='.basename($filename));
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: '.filesize(APP_PATH.$filename));
		
		readfile(APP_PATH.$filename);
		
		self::completeRequest();
	}
	
	/**
	 * Rebuilds $_GET. Necessary due to .htaccess re-routing.
	 */
	private static function rebuildGet()
	{
		$url = $_SERVER['REQUEST_URI'];
		$pos = strpos($url, '?');
		
		if($pos !== false)
		{
			parse_str(substr($url, $pos+1, (strlen($url)-($pos+1))), $_GET);
		}
	}
}