<?php

namespace ApiApp\Serializer;

abstract class BaseSerializer{
	
	protected $mime;
	
	/**
	 * @var Zend_Controller_Router_Interface
	 */
	protected $router;
	
	public function __construct($mime){
		$this->mime = $mime;
		$this->router = \Zend_Controller_Front::getInstance()->getRouter();
	}	
}