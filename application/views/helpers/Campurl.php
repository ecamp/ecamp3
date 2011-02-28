<?php

namespace Application\View\Helper;

class Campurl extends \Zend_View_Helper_Abstract
{
	public $view;

	public function __call($name, $arguments)
	{
		if( $name == "campurl" )
			return call_user_func_array(array($this,"execute"), $arguments);
	}
	
	/**
	 * this helper automatically adds the camp parameter to the url
	 */
    public function execute( $urlOptions, $name = null, $reset = false, $encode = true)
    {
        $urlOptions['camp'] = \Zend_Controller_Front::getInstance()->getRequest()->getParam('camp');
	    
	    if( ! isset($urlOptions['controller']))
	       $urlOptions['controller'] = \Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
			       
        return $this->view->url( $urlOptions, $name, $reset, $encode);
    }

    public function setView(\Zend_View_Interface $view)
    {
        $this->view = $view;
    }
}