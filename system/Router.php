<?php

/*======================================
Kodexy Framework v0.8.2
Author: Matt Larsen
Web: github.com/mattlarsen/kodexy
======================================*/

namespace Kodexy;

/**
 * Routes requests.
 */
class Router
{
    private $request;
    private $params;
    
    /**
     * Gets the request (controller and parameters as specified in the URL).
     */
    public function getRequest()
    {
        return $this->request;
    }
    
    /**
     * Gets the parameters for the request (URL segments after the controller).
     * @param $index - 0-based index of the parameter.
     * @param $defaultValue - to return if the parameter doesn't exist
     */
    public function getParam($index, $defaultValue = null)
    {
        if (isset($this->params[$index]))
        {
            return $this->params[$index];
        }
        
        return $defaultValue;
    }
    
    /**
     * Routes the request to the associated controller.
     */
    public function route()
    {
        $request = '';
        if (isset($_GET['q']))
        {
            $request = $_GET['q'];
        }
        
        $this->request = $request;
        $this->rebuildGet();
        
        //get controller
        $found = false;
        
        if ($request == '')
        {
            $request = 'index';
        }
        
        $segments = explode('/', $request);
        $dir = APP_PATH.'controllers/';
        foreach ($segments as $index => $segment)
        {
            $segment = str_replace('-', '_', $segment);
            if (file_exists($dir.$segment.'.php'))
            {
                //load controller
                $found = true;
                $this->params = array_slice($segments, $index+1);
                require($dir.$segment.'.php');
                break;
            }
            else
            {
                $dir .= $segment.'/';
            }
        }
        
        if (!$found)
        {
            //try index controller
            if (file_exists($dir.'index.php'))
            {
                //load controller
                $found = true;
                $this->params = array();
                require($dir.'index.php');
            }
        }
        
        if (!$found)
        {
            $this->notFound();
        }
        
        $this->completeRequest();
    }
    
    /**
     * Performs final closing-down tasks.
     */
    public function completeRequest()
    {
        if (kodexy()->database->isConnected())
        {
            kodexy()->database->disconnect();
        }
        
        exit;
    }
    
    /**
     * Shows a 404 Not Found page.
     */
    public function notFound()
    {
        http_response_code(404);
        error_log('404 page not found: '.$this->getRequest());
        kodexy()->loadView('system/notFound');
        $this->completeRequest();
    }
    
    /**
     * Redirects the browser to another URL.
     * @param $url
     */
    public function redirect($url)
    {
        if (strpos($url, '://') === false) //relative URL
        {
            $url = BASE_URL.$url;
        }
        
        header('Location: '.$url); //redirect
        exit;
    }
    
    /**
     * Displays the specified file inline.
     * @param $filename - filename extending from the application directory (e.g. "uploads/A7shG.jpg").
     * @param $mime
     */
    public function displayFile($filename, $mime)
    {
        header('Content-Type: '.$mime);
        header('Content-Length: '.filesize(APP_PATH.$filename));
        header('Content-Disposition: inline; filename='.basename($filename));
        
        readfile(APP_PATH.$filename);
        
        $this->completeRequest();
    }
    
    /**
     * Forces the client's browser to download the file specified.
     * @param $filename - filename extending from the application directory (e.g. "uploads/uISH5.docx").
     * @param $mime
     */
    public function forceDownload($filename, $mime = 'application/octet-stream')
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
        
        $this->completeRequest();
    }
    
    /**
     * Rebuilds $_GET. Necessary due to .htaccess re-routing.
     */
    private function rebuildGet()
    {
        $url = $_SERVER['REQUEST_URI'];
        $pos = strpos($url, '?');
        
        if ($pos !== false)
        {
            parse_str(substr($url, $pos+1, (strlen($url)-($pos+1))), $_GET);
        }
    }
}