<?php

namespace WebApp\View\Helper;

class Thisurl extends \Zend_View_Helper_Abstract
{
	public $view;

	public function __call($name, $arguments)
	{
		if( $name == "thisurl" )
			return call_user_func_array(array($this,"execute"), $arguments);
	}

    public function execute( $urlOptions, $name = null, $reset = false, $encode = true)
    {
	    if( ! isset($urlOptions['controller']))
	    	$urlOptions['controller'] = \Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
		
	    if( ! isset($urlOptions['action']))
	    	$urlOptions['action'] = '';
	       
        return $this->view->url( $urlOptions, $name, $reset, $encode);
    }

    public function setView(\Zend_View_Interface $view)
    {
        $this->view = $view;
    }

}