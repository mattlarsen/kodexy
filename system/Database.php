<?php

/*======================================
Kodexy Framework v0.8.2
Author: Matt Larsen
Web: github.com/mattlarsen/kodexy
======================================*/

namespace Kodexy;

/**
 * Controls database interaction.
 */
class Database
{
    private $connection;
    private $isConnected = false;
    
    /**
     * Connect to the database.
     * @param $dsn - PDO DSN. e.g. mysql:host=localhost;dbname=someDatabase;charset=utf8
     * @param $username
     * @param $password
     * @param $driverOptions - see PDO::setAttribute
     */
    public function connect($dsn, $username, $password, $driverOptions)
    {
        try
        {
            //connect to database
            $this->connection = new \PDO($dsn, $username, $password, $driverOptions);
            
            if (kodexy()->getConfig('displayErrors') && !isset($driverOptions[\PDO::ATTR_ERRMODE]))
            {
                $this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION); //show errors
            }
            
            if (strpos($dsn, 'mysql') !== false)
            {
                $this->connection->exec('SET NAMES utf8'); //fix MySQL charset security bug in certain PHP versions.
            }
            
            $this->isConnected = true;
        }
        catch (\PDOException $e) 
        {
            error_log('Database connection failed. Error: '.$e->getMessage());
            
            if (kodexy()->getConfig('displayErrors'))
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
    public function disconnect()
    {
        $this->connection = null;
        $this->isConnected = false;
    }
    
    /**
     * Returns whether or not there is an active connection to the database.
     */
    public function isConnected()
    {
        return $this->isConnected;
    }
    
    /**
     * Perform an SQL query. Returns the result.
     * @param $type - "rows", "row", "column" (single value) or "execute" (UPDATE/DELETE)
     * @param $sql - the SQL to run, using question marks as placeholders.
     * @param $args - the array of values used to replace the placeholders within $sql.
     */
    private function query($type, $sql, $args = array())
    {
        $stmt = $this->connection->prepare($sql);
        
        //bind parameters
        if (count($args))
        foreach (array_values($args) as $key => $arg)
        {
            $key++; //bindValue() is 1-based
            
            if (is_int($arg))
            {
                $stmt->bindValue($key, $arg, \PDO::PARAM_INT);
            }
            else if (is_null($arg))
            {
                $stmt->bindValue($key, $arg, \PDO::PARAM_NULL);
            }
            else if (is_bool($arg))
            {
                $stmt->bindValue($key, $arg, \PDO::PARAM_BOOL);
            }
            else
            {
                $stmt->bindValue($key, $arg, \PDO::PARAM_STR);
            }
        }
        
        //execute SQL
        $exec = $stmt->execute();
        
        //return results
        if ($type == 'rows')
        {
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }
        else if ($type == 'row')
        {
            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            if (count($rows))
            {
                return $rows[0];
            }
            
            return array();
        }
        else if ($type == 'column')
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
     * @param $sql - see query()
     * @param $args - see query()
     */
    public function getRows($sql, $args = array())
    {
        return $this->query('rows', $sql, $args);
    }
    
    /**
     * Returns a single row from a SELECT query. Shortcut to query().
     * @param $sql - see query()
     * @param $args - see query()
     */
    public function getRow($sql, $args = array())
    {
        return $this->query('row', $sql, $args);
    }
    
    /**
     * Returns a single value from a SELECT query (must select only one column). Good for selecting row count results. Shortcut to query().
     * @param $sql - see query()
     * @param $args - see query()
     */
    public function getColumn($sql, $args = array())
    {
        return $this->query('column', $sql, $args);
    }
    
    /**
     * Executes an SQL statement. Shortcut to query().
     * @param $sql - see query()
     * @param $args - see query()
     */
    public function execute($sql, $args = array())
    {
        return $this->query('execute', $sql, $args);
    }
    
    /**
     * Returns the ID of the last row inserted.
     */
    public function getLastId()
    {
        return $this->connection->lastInsertId();
    }
}