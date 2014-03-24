<?php

/*======================================
Kodexy Framework v0.8.2
Author: Matt Larsen
Web: github.com/mattlarsen/kodexy
======================================*/

/**
 * Utility functions
 */

/**
 * Returns the main Kodexy instance.
 */
function kodexy()
{
    global $kodexy;
    return $kodexy;
}

/**
 * Merges the values of multiple arrays into one array ignoring keys.
 */
function mergeValues(/*arrays to merge*/)
{
    $arrays = func_get_args();
    $all = array();
    
    foreach ($arrays as $array)
    {
        if (count($array))
        foreach ($array as $val)
        {
            $all[] = $val;
        }
    }
    
    return $all;
}

/**
 * Formats bytes as B, KB, MB, GB, or TB appropriately for output.
 * @param $bytes
 * @param $precision - decimal places
 */
function formatBytes($bytes, $precision = 2) 
{ 
    $units = array('B', 'KB', 'MB', 'GB', 'TB'); 

    $bytes = max($bytes, 0); 
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
    $pow = min($pow, count($units) - 1); 
    
    $bytes /= pow(1024, $pow);
    
    return round($bytes, $precision).' '.$units[$pow]; 
}

/**
 * Shortcut to htmlspecialchars for XSS escaping.
 * @param $data - String(s) to escape.
 */
function xss($data)
{
    if (is_array($data))
    {
        foreach ($data as &$var)
        {
            $var = xss($var);
        }
    }
    else
    {
        return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }
    
    return $data;
}

/**
 * Shortcut to html_entity_decode. Used to un-escape XSS-escaped data.
 * @param $data - String(s) to un-escape.
 */
function unxss($data)
{
    if (is_array($data))
    {
        foreach ($data as &$var)
        {
            $var = unxss($var);
        }
    }
    else
    {
        return html_entity_decode($data, ENT_QUOTES, 'UTF-8');
    }
    
    return $data;
}

/**
 * Generates a random string of a given length.
 * @param $length
 */
function randomString($length)
{
    $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijqlmnopqrtsuvwxyz0123456789';
    $max = strlen($chars)-1;

    $string = '';
    for ($i=0; $i<$length; $i++)
    {
        $rand = (int)mt_rand(0.0, $max);
        $string .= $chars[$rand];
    }
    
    return $string;
}

/**
 * Returns the file extension for the given filename.
 * @param $filename
 */
function getFileExtension($filename)
{
    $bits = explode('.', $filename);
    return strtolower(end($bits));
}

/**
 * Backwards compatibility with PHP <5.4
 */
if (!function_exists('http_response_code'))
{
    function http_response_code($httpStatusCode = 200)
    {
        header('X-PHP-Response-Code: '.$httpStatusCode, true, $httpStatusCode);
    }
}

/**
 * Renders an array of attributes into HTML.
 * @param $attributes - associative array of attributes. Keys = attributes, values = values.
 */
function renderHtmlAttributes($attributes)
{
    //render
    $attributesHtml = '';
    foreach ($attributes as $attribute => $value)
    {
        $attributesHtml .= $attribute.'="'.$value.'" ';
    }
    
    return $attributesHtml;
}