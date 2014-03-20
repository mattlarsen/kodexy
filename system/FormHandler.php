<?php

/*======================================
Kodexy Framework v0.8.1
Author: Matt Larsen
Web: perthcomputing.com/projects/kodexy
======================================*/

/**
 * Form validation.
 */
class FormHandler
{
	private static $errors;
	
	/**
	 * Add an error message for a particular form field.
	 * @param $name - HTML element name.
	 */
	public static function addError($name, $errorMessage)
	{
		self::$errors[$name][] = $errorMessage;
	}
	
	/**
	 * Convert a HTML name to an appropriate HTML id.
	 * @param $name
	 * @param $value - used as a suffix to ensure uniqueness.
	 */
	public static function convertNameToId($name, $value = NULL)
	{
		$str = 'edit-'.str_replace(array('[', ']'), array('-', ''), $name);
		
		if($value !== NULL)
		{
			if(substr($str, -1, 1) != '-')
			{
				$str .= '-';
			}
			
			$str .= $value;
		}
		
		return $str;
	}
	
	/**
	 * Ensures that posted options are within the allowed options (used for select boxes).
	 */
	public static function validateOptions($values, $options)
	{
		if(!is_array($values)) //convert to multiple select
		{
			$values = array($values);
		}
		
		foreach ($values as $value)
		{
			if(!in_array($value, $options))
			{
				error_log('Security exception: invalid select option. IP: '.$_SERVER['REMOTE_ADDR']);
				
				if(Kodexy::$config['development'])
				{
					die('Security exception: invalid select option.');
				}
				else
				{
					die('Security exception.');
				}
			}
		}
	}
	
	/**
	 * Returns whether or not there are any errors logged.
	 * @param $name - HTML element name, (blank) check all errors, or (non-blank) errors for a given element.
	 * @param $validateCsrf - whether or not to validate a posted CSRF value.
	 */
	public static function isErrors($name = '', $validateCsrf = TRUE)
	{
		if($name == '')
		{
			if($validateCsrf && count($_POST))
			{
				Session::validateCsrfToken($_POST['_csrf']);
			}
			
			return (count(self::$errors) > 0); //all errors
		}
		else
		{
			return isset(self::$errors[$name]); //errors for a particular field
		}
	}
	
	/**
	 * Returns any errors logged.
	 * @param $name - HTML element name, (blank) return all errors, or (non-blank) errors for a given element.
	 */
	public static function getErrors($name = '')
	{
		$errors = array();
		
		if($name == '') //all errors
		{
			foreach (self::$errors as $name => $refErrors)
			{
				foreach ($refErrors as $error)
				{
					$errors[] = $error;
				}
			}
		}
		else //errors for a particular field
		{
			foreach (self::$errors[$name] as $error)
			{
				$errors[] = $error;
			}
		}
		
		return $errors;
	}
	
	/**
	 * Empties the log of errors.
	 */
	public static function reset()
	{
		self::$errors = array();
	}
	
	/**
	 * Returns whether or not a file has been uploaded for a particular file form field.
	 * @param $error - error value provided in $_FILES.
	 */
	public static function fileIsset($error)
	{
		return ($error != UPLOAD_ERR_NO_FILE);
	}
	
	/**
	 * Returns TRUE if an error occurred while uploading a file.
	 * @param $error - error value in $_FILES.
	 */
	public static function isFileError($error)
	{
		return ($error != UPLOAD_ERR_NO_FILE && $error != UPLOAD_ERR_OK);
	}

	/**
	 * Returns TRUE if the given email address is formatted correctly.
	 */
	public static function isValidEmail($email)
	{
		return (filter_var($email, FILTER_VALIDATE_EMAIL) !== FALSE);
	}
	
	/**
	 * Get the value of an element by its HTML name. E.g., $name = "user[12]" will return $_POST['user'][12].
	 */
	public static function getPost($name)
	{
		$element = $_POST;
		
		$name = str_replace(']', '', $name);
		$levels = explode('[', $name);
		if(count($levels))
		foreach ($levels as $level)
		{
			if($level == '')
			{
				break;
			}
			if(isset($element[$level]))
			{
				$element = $element[$level];
			}
			else
			{
				return NULL;
			}
		}
		
		return $element;
	}
}