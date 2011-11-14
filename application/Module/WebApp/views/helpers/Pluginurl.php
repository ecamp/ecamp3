<?php

namespace WebApp\View\Helper;

class Pluginurl extends \Zend_View_Helper_Abstract
{
	public $view;

	public function __call($name, $arguments)
	{
		if( $name == "pluginurl" )
			return call_user_func_array(array($this,"execute"), $arguments);
	}

    public function execute( $plugin )
    {
		$camp = $plugin->getEvent()->getCamp();
		
		$urlOptions = array();
	    $urlOptions['controller'] = 'event';
		$urlOptions['action']     = 'plugin';
		$urlOptions['camp']       = $camp->getId();
		$urlOptions['id']         = $plugin->getId();
		
		if( $camp->belongsToUser() )
		{
			$urlOptions['user']       = $camp->getOwner()->getId();
			$route = 'user+camp+id';
		}
		else
		{
			$urlOptions['group']       = $camp->getGroup()->getId();
			$route = 'group+camp+id';
		}
			       
        return $this->view->url( $urlOptions, $route);
    }

    public function setView(\Zend_View_Interface $view)
    {
        $this->view = $view;
    }

}