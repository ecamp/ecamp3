<?php

namespace Core\Error;

use Zend_Controller_Front;
use Zend_Controller_Plugin_Abstract;
use Zend_Controller_Request_Abstract;

class MinimalErrorHandler extends Zend_Controller_Plugin_Abstract
{
	/**
	 * @var Zend_Controller_Plugin_Abstract
	 */
	protected $errorHandler = null;
	
	/**
	 * @var \Zend_Controller_Front
	 */
	protected $front;
	
	
	public function __construct(Zend_Controller_Front $front){
		$this->front = $front;
	}
	
	
	public function addErrorHandler(Zend_Controller_Plugin_Abstract $errorHandler){
		if($errorHandler != $this->errorHandler){
			if($this->errorHandler != null){
				$this->front->unregisterPlugin($this->errorHandler);
			}
			
			$this->errorHandler = $errorHandler;
			$this->front->registerPlugin($this->errorHandler);
		}
	}
	
	
	public function routeShutdown(Zend_Controller_Request_Abstract $request){
		echo $request->getModuleName();
		
		if($this->errorHandler == null){
			$this->_handleError($request);
		} else {
			$this->errorHandler->routeShutdown($request);
		}
	}
	
	public function preDispatch(Zend_Controller_Request_Abstract $request){
		if($this->errorHandler == null){
			$this->_handleError($request);
		} else {
			$this->errorHandler->routeShutdown($request);
		}
	}
	
	public function postDispatch(Zend_Controller_Request_Abstract $request){
		if($this->errorHandler == null){
			$this->_handleError($request);
		} else {
			$this->errorHandler->routeShutdown($request);
		}
	}
	
	protected function _handleError(Zend_Controller_Request_Abstract $request)
	{
		$response = $this->getResponse();
		
		$response->isException() ? die("Exception") : null; 
		
		if ($response->isException()){
			echo "Minimal Error Handler: <br /><pre>";
			var_dump($response->getException());
			echo "</pre>";
			die();
		}
	}
}
