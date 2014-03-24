<?php

/*======================================
Kodexy Framework v0.8.2
Author: Matt Larsen
Web: github.com/mattlarsen/kodexy
======================================*/

namespace Kodexy;

/**
 * Form validation.
 */
class FormHandler
{
    private $errors;
    
    /**
     * Add an error message for a particular form field.
     * @param $name - HTML element name.
     * @param $errorMessage
     */
    public function addError($name, $errorMessage)
    {
        $this->errors[$name][] = $errorMessage;
    }
    
    /**
     * Convert a HTML name to an appropriate HTML id.
     * @param $name
     * @param $value - used as a suffix to ensure uniqueness.
     */
    public function convertNameToId($name, $value = null)
    {
        $str = 'edit-'.str_replace(array('[', ']'), array('-', ''), $name);
        
        if ($value !== null)
        {
            if (substr($str, -1, 1) != '-')
            {
                $str .= '-';
            }
            
            $str .= $value;
        }
        
        return $str;
    }
    
    /**
     * Ensures that posted options are within the allowed options (used for select boxes).
     * @param $values
     * @param $options
     */
    public function validateOptions($values, $options)
    {
        if (!is_array($values)) //convert to multiple select
        {
            $values = array($values);
        }
        
        foreach ($values as $value)
        {
            if (!in_array($value, $options))
            {
                error_log('Security exception: invalid select option. IP: '.$_SERVER['REMOTE_ADDR']);
                
                if (kodexy()->getConfig('displayErrors'))
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
    public function isErrors($name = '', $validateCsrf = true)
    {
        if ($name == '')
        {
            if ($validateCsrf && count($_POST))
            {
                kodexy()->session->validateCsrfToken($_POST['_csrf']);
            }
            
            return (count($this->errors) > 0); //all errors
        }
        else
        {
            return isset($this->errors[$name]); //errors for a particular field
        }
    }
    
    /**
     * Returns any errors logged.
     * @param $name - HTML element name, (blank) return all errors, or (non-blank) errors for a given element.
     */
    public function getErrors($name = '')
    {
        $errors = array();
        
        if ($name == '') //all errors
        {
            foreach ($this->errors as $name => $refErrors)
            {
                foreach ($refErrors as $error)
                {
                    $errors[] = $error;
                }
            }
        }
        else //errors for a particular field
        {
            foreach ($this->errors[$name] as $error)
            {
                $errors[] = $error;
            }
        }
        
        return $errors;
    }
    
    /**
     * Empties the log of errors.
     */
    public function reset()
    {
        $this->errors = array();
    }
    
    /**
     * Returns whether or not a file has been uploaded for a particular file form field.
     * @param $error - error value provided in $_FILES.
     */
    public function fileIsset($error)
    {
        return ($error != UPLOAD_ERR_NO_FILE);
    }
    
    /**
     * Returns true if an error occurred while uploading a file.
     * @param $error - error value in $_FILES.
     */
    public function isFileError($error)
    {
        return ($error != UPLOAD_ERR_NO_FILE && $error != UPLOAD_ERR_OK);
    }

    /**
     * Returns true if the given email address is formatted correctly.
     * @param $email
     */
    public function isValidEmail($email)
    {
        return (filter_var($email, FILTER_VALIDATE_EMAIL) !== false);
    }
    
    /**
     * Get the value of an element by its HTML name. E.g., $name = "user[12]" will return $_POST['user'][12].
     * @param $name
     */
    public function getPost($name)
    {
        $element = $_POST;
        
        $name = str_replace(']', '', $name);
        $levels = explode('[', $name);
        if (count($levels))
        foreach ($levels as $level)
        {
            if ($level == '')
            {
                break;
            }
            if (isset($element[$level]))
            {
                $element = $element[$level];
            }
            else
            {
                return null;
            }
        }
        
        return $element;
    }
}