<?php
/*
 * Copyright (C) 2011 Pirmin Mattmann
 *
 * This file is part of eCamp.
 *
 * eCamp is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * eCamp is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with eCamp.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Core\Module;


class BootstrapPlugin
	extends \Zend_Controller_Plugin_Abstract
{
	
	private $bootstrap;
	
	private $methods = null;
	
	
	public function __construct(\Zend_Application_Module_Bootstrap $bootstrap)
	{
		$this->bootstrap = $bootstrap;
	}
	
	private function isActive(){
		//echo $this->getRequest()->getModuleName() . "\n";
		return $this->getRequest()->getModuleName() == $this->bootstrap->getModuleName();
	}
	
	private function callMethodsStartingWith($prefix, $request)
	{
		$prefixLength = strlen($prefix);
		
		if(is_null($this->methods))
		{	$this->methods = get_class_methods($this->bootstrap);	}
		
		foreach($this->methods as $method)
		{
			if(substr($method, 0, $prefixLength) == $prefix)
			{	call_user_func(array($this->bootstrap, $method), $request);	}
		}
	}
	
	
	
	public function routeStartup(\Zend_Controller_Request_Abstract $request) {
		//echo __METHOD__ . ": ";
		
	}
	
	public function routeShutdown(\Zend_Controller_Request_Abstract $request) {
		//echo __METHOD__ . ": ";
		if($this->isActive()){
			$this->callMethodsStartingWith("_routeShutdown", $request);
		}
	}
	
	public function dispatchLoopStartup(\Zend_Controller_Request_Abstract $request) {
		//echo __METHOD__ . ": ";
		if($this->isActive()){
			$this->callMethodsStartingWith("_dispatchLoopStartup", $request);
		}
	}
	
	public function preDispatch(\Zend_Controller_Request_Abstract $request) {
		//echo __METHOD__ . ": ";
		if($this->isActive()){
			$this->callMethodsStartingWith("_preDispatch", $request);
		}
	}
	
	public function postDispatch(\Zend_Controller_Request_Abstract $request) {
		//echo __METHOD__ . ": ";
		if($this->isActive()){
			$this->callMethodsStartingWith("_postDispatch", $request);
		}
	}
	
	public function dispatchLoopShutdown() {
		//echo __METHOD__ . ": ";
		if($this->isActive()){
			$this->callMethodsStartingWith("_dispatchLoopShutdown", null);
		}
	}
	
}