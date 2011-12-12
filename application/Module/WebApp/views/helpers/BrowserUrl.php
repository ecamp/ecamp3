<?php

namespace WebApp\View\Helper;

class BrowserUrl extends \Zend_View_Helper_Abstract
{
	
	public function browserUrl($url = '/')
	{
		$this->view->BrowserUrlScript = 
		"<script type='text/javascript'> window.history.replaceState({}, '', '$url'); </script>";
	}
	
}