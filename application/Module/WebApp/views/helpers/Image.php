<?php

namespace WebApp\View\Helper;

class Image extends \Zend_View_Helper_Abstract
{
	public $view;

	public function __call($name, $arguments)
	{
		if( $name == "Image" )
			return call_user_func_array(array($this,"execute"), $arguments);
	}
	
	/**
	 * this helper automatically adds the camp parameter to the url
	 */
    public function execute($path)
    {
    	$path = PUBLIC_PATH . $path;
    	
    	if(!file_exists($path))	
    		return "";
    	
    	$path_parts = pathinfo($path);
    	
    	$contents = file_get_contents($path);
    	$base64 = base64_encode($contents);

    	$mime = "image/" . $path_parts['extension'];
    	
    	return "data:$mime;base64,$base64";
    }

    public function setView(\Zend_View_Interface $view)
    {
        $this->view = $view;
    }
}


function data_uri($file, $mime) {
	$contents = file_get_contents($file);
	$base64 = base64_encode($contents);
	return "data:$mime;base64,$base64";
}