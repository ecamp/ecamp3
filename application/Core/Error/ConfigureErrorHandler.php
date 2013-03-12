<?php

namespace Core\Error;

use Zend_Controller_Plugin_Abstract;
use Zend_Controller_Plugin_ErrorHandler;
use Zend_Controller_Request_Abstract;
use Zend_Controller_Response_Abstract;

class ConfigureErrorHandler 
	extends Zend_Controller_Plugin_Abstract
{
	
	/**
	 * @var \Zend_Controller_Plugin_ErrorHandler
	 */
	private $errorHandler = null;
	
	private $module = null;
	private $controller = null;
	private $action = null;
	
	public function __construct( 
		Zend_Controller_Plugin_ErrorHandler $errorHandler,
		$module,
		$controller = null,
		$action = null
	){
		$this->errorHandler = $errorHandler;
		
		$this->module = $module;
		$this->controller = $controller;
		$this->action = $action;
	}
	
	
	/**
	 * Called before Zend_Controller_Front begins evaluating the
	 * request against its routes.
	 *
	 * @param Zend_Controller_Request_Abstract $request
	 * @return void
	 */
	public function routeStartup(Zend_Controller_Request_Abstract $request){
		$this->configureErrorHandler($request);
	}
	
	/**
	 * Called after Zend_Controller_Router exits.
	 *
	 * Called after Zend_Controller_Front exits from the router.
	 *
	 * @param  Zend_Controller_Request_Abstract $request
	 * @return void
	 */
	public function routeShutdown(Zend_Controller_Request_Abstract $request){
		$this->configureErrorHandler($request);
	}
	
	/**
	 * Called before Zend_Controller_Front enters its dispatch loop.
	 *
	 * @param  Zend_Controller_Request_Abstract $request
	 * @return void
	 */
	public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request){
		$this->configureErrorHandler($request);
	}
	
	/**
	 * Called before an action is dispatched by Zend_Controller_Dispatcher.
	 *
	 * This callback allows for proxy or filter behavior.  By altering the
	 * request and resetting its dispatched flag (via
	 * {@link Zend_Controller_Request_Abstract::setDispatched() setDispatched(false)}),
	 * the current action may be skipped.
	 *
	 * @param  Zend_Controller_Request_Abstract $request
	 * @return void
	 */
	public function preDispatch(Zend_Controller_Request_Abstract $request){
		$this->configureErrorHandler($request);
	}
	
	/**
	 * Called after an action is dispatched by Zend_Controller_Dispatcher.
	 *
	 * This callback allows for proxy or filter behavior. By altering the
	 * request and resetting its dispatched flag (via
	 * {@link Zend_Controller_Request_Abstract::setDispatched() setDispatched(false)}),
	 * a new action may be specified for dispatching.
	 *
	 * @param  Zend_Controller_Request_Abstract $request
	 * @return void
	 */
	public function postDispatch(Zend_Controller_Request_Abstract $request){
		$this->configureErrorHandler($request);
	}
	
	
	private function configureErrorHandler(Zend_Controller_Request_Abstract $request){
		if($request->getModuleName() == $this->module){
			$this->errorHandler->setErrorHandlerModule($this->module);
				
			if($this->controller != null){
				$this->errorHandler->setErrorHandlerController($this->controller);
			}
				
			if($this->action != null){
				$this->errorHandler->setErrorHandlerAction($this->action);
			}
		}
	}
	
}