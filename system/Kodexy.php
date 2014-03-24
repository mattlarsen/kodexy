<?php

/*======================================
Kodexy Framework v0.8.2
Author: Matt Larsen
Web: github.com/mattlarsen/kodexy
======================================*/

namespace Kodexy;

/**
 * Core of the Kodexy framework.
 */
class Kodexy
{
    //classes
    public $database;
    public $formHandler;
    public $router;
    public $session;
    
    private $config; //from config.php
    private $pageTitle;
    private $pageHead;
    
    /**
     * Loads components and routes the request.
     */
    public function bootstrap()
    {
        //load classes
        $this->database = new Database();
        $this->formHandler = new FormHandler();
        $this->router = new Router();
        $this->session = new Session();
        
        //load config
        $this->config = require(APP_PATH.'config.php'); 
        
        define('BASE_URL', $this->getConfig('baseUrl'));
        $this->pageTitle = $this->getConfig('pageTitle');
        $this->pageHead = '';
        
        //error reporting
        error_reporting(E_ALL|E_STRICT);
        if (!$this->getConfig('displayErrors'))
        {
            //log, but don't display PHP errors.
            ini_set('display_errors', 0);
            ini_set('log_errors', 1);
        }
        
        //connect to database
        if ($this->getConfig('dbAutoConnect'))
        {
            $this->database->connect(
                $this->getConfig('dbDsn'),
                $this->getConfig('dbUsername'),
                $this->getConfig('dbPassword'),
                $this->getConfig('dbDriverOptions')
            );
        }
        
        //startup
        require(APP_PATH.'startup.php');
        
        //process request
        $this->router->route();
    }
    
    /**
     * Returns a config item.
     * @param $item
     */
    public function getConfig($item)
    {
        return $this->config[$item];
    }
    
    /**
     * Returns the page title.
     */
    public function getPageTitle()
    {
        return $this->pageTitle;
    }
    
    /**
     * Sets the page title.
     * @param $newTitle
     */
    public function setPageTitle($newTitle)
    {
        $this->pageTitle = $newTitle;
    }
    
    /**
     * Returns HTML to display in <head></head>
     */
    public function getPageHead()
    {
        return $this->pageHead;
    }
    
    /**
     * Sets HTML to display in <head></head>
     * @param $newHead
     */
    public function setPageHead($newHead)
    {
        $this->pageHead = $newHead;
    }
    
    /**
     * Adds a message to be displayed on the next page load. Useful for form submissions.
     * @param $message
     */
    public function addMessage($message)
    {
        $_SESSION['kodexy']['messages'][] = $message;
    }
    
    /**
     * Adds an error message to be displayed on the next page load.
     * @param $message
     */
    public function addErrorMessage($message)
    {
        $_SESSION['kodexy']['errorMessages'][] = $message;
    }
    
    /**
     * Returns whether or not there are messages to be displayed.
     */
    public function isMessages()
    {
        return (count($_SESSION['kodexy']['messages']) > 0);
    }
    
    /**
     * Returns whether or not there are error messages to be displayed.
     */
    public function isErrorMessages()
    {
        return (count($_SESSION['kodexy']['errorMessages']) > 0);
    }
    
    /**
     * Returns an array of session messages.
     */
    public function getMessages()
    {
        $messages = $_SESSION['kodexy']['messages'];
        $_SESSION['kodexy']['messages'] = array();
        return $messages;
    }
    
    /**
     * Returns an array of session error messages.
     */
    public function getErrorMessages()
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
    public function loadView($view, $data = array())
    {
        if ($this->getConfig('autoFilterXss'))
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
    public function loadCode($code)
    {
        $file = APP_PATH.'code/'.$code.'.php';
        $newDir = dirname($file);
        $curDir = getcwd();
        
        chdir($newDir);
        require($file);
        chdir($curDir);
    }
}