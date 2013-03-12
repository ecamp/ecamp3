<?php

namespace Core\Error;

use Zend_Controller_Plugin_Abstract;
use Zend_Controller_Plugin_ErrorHandler;
use Zend_Controller_Request_Abstract;
use Zend_Controller_Response_Abstract;

class ErrorHandleWrapper 
	extends Zend_Controller_Plugin_Abstract
{
	
	/**
	 * @var \Zend_Controller_Plugin_ErrorHandler
	 */
	private $errorHandler = null;
	
	private $module = null;
	
	public function __construct($module, Zend_Controller_Plugin_ErrorHandler $errorHandler){
		$this->module = $module;
		$this->errorHandler = $errorHandler;
	}
	
	
	/**
	 * Set request object
	 *
	 * @param Zend_Controller_Request_Abstract $request
	 * @return Zend_Controller_Plugin_Abstract
	 */
	public function setRequest(Zend_Controller_Request_Abstract $request)
	{
		$this->errorHandler->setRequest($request);
		return $this;
	}
	
	/**
	 * Get request object
	 *
	 * @return Zend_Controller_Request_Abstract $request
	 */
	public function getRequest()
	{
		return $this->errorHandler->getRequest();
	}
	
	/**
	 * Set response object
	 *
	 * @param Zend_Controller_Response_Abstract $response
	 * @return Zend_Controller_Plugin_Abstract
	 */
	public function setResponse(Zend_Controller_Response_Abstract $response)
	{
		$this->errorHandler->setResponse($response);
		return $this;
	}
	
	/**
	 * Get response object
	 *
	 * @return Zend_Controller_Response_Abstract $response
	 */
	public function getResponse()
	{
		return $this->errorHandler->getResponse();
	}
	
	/**
	 * Called before Zend_Controller_Front begins evaluating the
	 * request against its routes.
	 *
	 * @param Zend_Controller_Request_Abstract $request
	 * @return void
	 */
	public function routeStartup(Zend_Controller_Request_Abstract $request)
	{
		if($request->getModuleName() == $this->module){
// 			echo "routeStartup: " . $this->module . "<br />\r\n";
			$this->errorHandler->routeStartup($request);
		}
	}
	
	/**
	 * Called after Zend_Controller_Router exits.
	 *
	 * Called after Zend_Controller_Front exits from the router.
	 *
	 * @param  Zend_Controller_Request_Abstract $request
	 * @return void
	 */
	public function routeShutdown(Zend_Controller_Request_Abstract $request)
	{
		if($request->getModuleName() == $this->module){
// 			echo "routeShutdown: " . $this->module . "<br />\r\n";
			$this->errorHandler->routeShutdown($request);
		}
	}
	
	/**
	 * Called before Zend_Controller_Front enters its dispatch loop.
	 *
	 * @param  Zend_Controller_Request_Abstract $request
	 * @return void
	 */
	public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
	{
		if($request->getModuleName() == $this->module){
// 			echo "dispatchLoopStartup: " . $this->module . "<br />\r\n";
			$this->errorHandler->dispatchLoopStartup($request);
		}
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
	public function preDispatch(Zend_Controller_Request_Abstract $request)
	{
		if($request->getModuleName() == $this->module){
// 			echo "preDispatch: " . $this->module . "<br />\r\n";
			$this->errorHandler->preDispatch($request);
		}
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
	public function postDispatch(Zend_Controller_Request_Abstract $request)
	{
		if($request->getModuleName() == $this->module){
// 			echo "postDispatch: " . $this->module . "<br />\r\n";
			$this->errorHandler->postDispatch($request);
		}
	}
	
	/**
	 * Called before Zend_Controller_Front exits its dispatch loop.
	 *
	 * @return void
	 */
	public function dispatchLoopShutdown()
	{
		$this->errorHandler->dispatchLoopShutdown();
	}
	
	
}