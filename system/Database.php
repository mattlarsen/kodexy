<?php

/*======================================
Kodexy Framework v0.8.1
Author: Matt Larsen
Web: perthcomputing.com/projects/kodexy
======================================*/

/**
 * Controls database interaction.
 */
class Database
{
	private static $connection;
	private static $isConnected = FALSE;
	
	/**
	 * Connect to the database.
	 * @param $database - (Optional) associative array of custom database connection settings (same keys as in config.php)
	 */
	public static function connect($database = NULL)
	{
		//get settings
		if($database === NULL)
		{
			$database = Kodexy::$config['database'];
		}
		
		try
		{
			//connect to database
			self::$connection = new PDO($database['dsn'], $database['username'], $database['password'], $database['driverOptions']);
			
			if(Kodexy::$config['development'] && !isset($database['driverOptions'][PDO::ATTR_ERRMODE]))
			{
				self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //show errors
			}
			
			if(strpos($database['dsn'], 'mysql') !== FALSE)
			{
				self::$connection->exec('SET NAMES utf8'); //fix MySQL charset security bug in certain PHP versions.
			}
			
			self::$isConnected = TRUE;
		}
		catch (PDOException $e) 
		{
			error_log('Database connection failed. Error: '.$e->getMessage());
			
			if(Kodexy::$config['development'])
			{
				die('Database connection failed. Error: '.$e->getMessage());
			}
			else
			{
				die('Database connection failed.');
			}
		}
	}
	
	/**
	 * Disconnect from the database.
	 */
	public static function disconnect()
	{
		self::$connection = NULL;
		self::$isConnected = FALSE;
	}
	
	/**
	 * Returns whether or not there is an active connection to the database.
	 */
	public static function isConnected()
	{
		return self::$isConnected;
	}
	
	/**
	 * Perform an SQL query. Returns the result.
	 * @param $sql - the SQL to execute, using question marks as placeholders.
	 * @param $args - the array of values used to replace the placeholders within $sql.
	 */
	private static function query($type, $sql, $args = array())
	{
		$stmt = self::$connection->prepare($sql);
		
		//bind parameters
		if(count($args))
		foreach(array_values($args) as $key => $arg)
		{
			$key++; //bindValue() is 1-based
			
			if(is_int($arg))
			{
				$stmt->bindValue($key, $arg, PDO::PARAM_INT);
			}
			else if(is_null($arg))
			{
				$stmt->bindValue($key, $arg, PDO::PARAM_NULL);
			}
			else if(is_bool($arg))
			{
				$stmt->bindValue($key, $arg, PDO::PARAM_BOOL);
			}
			else
			{
				$stmt->bindValue($key, $arg, PDO::PARAM_STR);
			}
		}
		
		//execute SQL
		$exec = $stmt->execute();
		
		//return results
		if($type == 'rows')
		{
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		}
		else if($type == 'row')
		{
			$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
			
			if(count($rows))
			{
				return $rows[0];
			}
			
			return array();
		}
		else if($type == 'column')
		{
			return $stmt->fetchColumn();
		}
		else //execute
		{
			return $exec;
		}
	}
	
	/**
	 * Returns multiple rows from a SELECT query. Shortcut to query().
	 */
	public static function getRows($sql, $args = array())
	{
		return self::query('rows', $sql, $args);
	}
	
	/**
	 * Returns a single row from a SELECT query. Shortcut to query().
	 */
	public static function getRow($sql, $args = array())
	{
		return self::query('row', $sql, $args);
	}
	
	/**
	 * Returns a single value from a SELECT query (must select only one column). Shortcut to query(). Good for selecting row count results.
	 */
	public static function getColumn($sql, $args = array())
	{
		return self::query('column', $sql, $args);
	}
	
	/**
	 * Executes an SQL statement. Shortcut to query().
	 */
	public static function execute($sql, $args = array())
	{
		return self::query('execute', $sql, $args);
	}
	
	/**
	 * Returns the ID of the last row inserted.
	 */
	public static function getLastId()
	{
		return self::$connection->lastInsertId();
	}
}