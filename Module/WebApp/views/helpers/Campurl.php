<?php

namespace WebApp\View\Helper;

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
    public function execute( $camp, $urlOptions, $name = null, $reset = false, $encode = true)
    {
	    $urlOptions['camp'] = $camp->getId();

		if( $camp->belongsToUser() )
		{
			$urlOptions['user']       = $camp->getOwner()->getId();
			$name = $name=='' ? 'user+camp' : 'user+'.$name;
		}
		else
		{
			$name = $name=='' ? 'group+camp' : 'group+'.$name;
			$urlOptions['group']       = $camp->getGroup()->getId();
		}

	    if( ! isset($urlOptions['controller']))
	       $urlOptions['controller'] = \Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
			       
        return $this->view->url( $urlOptions, $name, $reset, $encode);
    }

    public function setView(\Zend_View_Interface $view)
    {
        $this->view = $view;
    }
}
