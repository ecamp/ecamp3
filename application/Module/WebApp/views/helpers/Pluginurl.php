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

    public function execute( $plugin, $method )
    {
		return "http://api.ecamp3.dev/plugin/".$plugin->getId()."/".$method."/";
    }

    public function setView(\Zend_View_Interface $view)
    {
        $this->view = $view;
    }

}