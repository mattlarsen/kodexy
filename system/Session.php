<?php

/*======================================
Kodexy Framework v0.8.2
Author: Matt Larsen
Web: github.com/mattlarsen/kodexy
======================================*/

namespace Kodexy;

/**
 * Manages user sessions.
 */
class Session
{
    /**
     * Initializes session management for the request. To be called on start-up.
     */
    public function init()
    {
        //start session
        session_start();
        
        //create session if one doesn't yet exist
        if (!isset($_SESSION['kodexy']))
        {
            $this->createSession();
        }
        
        //security
        $this->updateSession();
    }
    
    /**
     * Create a new session.
     */
    public function createSession()
    {
        $_SESSION['kodexy'] = array(
            'csrf'                => randomString(32),
            'messages'            => array(),
            'errorMessages'       => array(),
            'lastActivity'        => time(),
            'created'             => time(),
            'logged'              => false,
        );
    }
    
    /**
     * Securely unset the current session.
     */
    public function destroySession()
    {
        $_SESSION = array();
        session_destroy();
        
        session_start(); //restart, so sessions can still be used.
    }
    
    /**
     * Security housekeeping.
     */
    public function updateSession()
    {
        $_SESSION['kodexy']['lastActivity'] = time();
        
        if (time() - $_SESSION['kodexy']['created'] > 1800) //session started more than 30 minutes ago
        {
            $this->regenerateSessionId(); //refresh session ID
        }
    }
    
    /**
     * Move to new session for security purposes.
     */
    public function regenerateSessionId()
    {
        //get data
        $sessionData = $_SESSION;
        
        //destroy user session
        $this->destroySession();
        
        //set data
        $_SESSION = $sessionData;
        $_SESSION['kodexy']['created'] = time();
    }
    
    /**
     * Returns the CSRF token for the session.
     */
    public function getCsrfToken()
    {
        return $_SESSION['kodexy']['csrf'];
    }
    
    /**
     * Validates the CSRF token for the session.
     * @param $token
     */
    public function validateCsrfToken($token)
    {
        if ($token != $_SESSION['kodexy']['csrf'])
        {
            error_log('Security exception: invalid CSRF token. IP: '.$_SERVER['REMOTE_ADDR']);
            
            if (kodexy()->getConfig('displayErrors'))
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
    public function login()
    {
        $this->destroySession();
        $this->createSession();
        $_SESSION['kodexy']['logged'] = true;
    }
    
    /**
     * Returns whether or not the user is logged into the system.
     */
    public function isLoggedIn()
    {
        return (bool)$_SESSION['kodexy']['logged'];
    }
    
    /**
     * Logs out a user from the system.
     */
    public function logout()
    {
        $this->destroySession();
        $this->createSession();
    }
}