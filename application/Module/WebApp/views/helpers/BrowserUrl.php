<?php

namespace WebApp\View\Helper;

class BrowserUrl extends \Zend_View_Helper_Abstract
{

	public function browserUrl($param = array(), $route = null)
	{
		$r = \Zend_Controller_Front::getInstance()->getRequest();
		
		$urlArray = 
			array(
				$r->getActionKey() 		=> $r->getActionName(), 
				$r->getControllerKey() 	=> $r->getControllerName(), 
				$r->getModuleKey() 		=> $r->getModuleName()
			);
		
		$urlArray = array_merge($urlArray, $param);
		
		$url = $this->view->url($urlArray, $route, true);
		
		$this->view->BrowserUrlScript = 
			"<script type='text/javascript'> window.history.replaceState({}, '', '$url'); </script>";
	}
	
}