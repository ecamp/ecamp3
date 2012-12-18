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

class ApiApp_Bootstrap extends Zend_Application_Module_Bootstrap
{

	protected function _initBootstrapPlugin()
	{
		$this->getApplication()->bootstrap('frontcontroller');
		$front = $this->getApplication()->getResource('frontcontroller');

		$front->registerPlugin(new \Core\Module\BootstrapPlugin($this));
	}

	
	protected function _initAutoloader()
	{
		require_once APPLICATION_PATH . '/../library/Doctrine/Common/ClassLoader.php';
	
		$autoloader = \Zend_Loader_Autoloader::getInstance();
	
		$navigationAutoloader = new \Doctrine\Common\ClassLoader('ApiApp', APPLICATION_PATH . '/Module/');
		$autoloader->pushAutoloader(array($navigationAutoloader, 'loadClass'), 'ApiApp');
	}

	
	protected function _initRoutes()
	{
		$hostname = Zend_Registry::get('hostname');
		
		/* Subdomain Route */
		$ApiSubdomain = new Zend_Controller_Router_Route_Hostname(
				"api." . $hostname, array('module' => 'ApiApp'));
		
		
		/* default Plugin Router */
		Zend_Controller_Front::getInstance()->getRouter()->addRoute(
			'plugin', $ApiSubdomain->chain(
				new Zend_Controller_Router_Route('/plugin/:id/:method/*',
				array('controller' => 'plugin', 'action' => 'index'), array('id' => '[0-9a-f]+'))));
		
		/* default Plugin Router */
		Zend_Controller_Front::getInstance()->getRouter()->addRoute(
			'general', $ApiSubdomain->chain(
				new Zend_Controller_Router_Route('/:controller/:action/*',
				array('controller' => 'error', 'action' => 'error'))));
		
		/* default Plugin Router */
		Zend_Controller_Front::getInstance()->getRouter()->addRoute(
			'general+id', $ApiSubdomain->chain(
				new Zend_Controller_Router_Route('/:controller/:action/:id/*',
				array('controller' => 'error', 'action' => 'error'), array('id' => '[0-9a-f]+'))));
	}
	
	/**
	 * Load and configure error handler
	 */
	public function _routeShutdown_ErrorHandler()
	{
		$plugin = new Zend_Controller_Plugin_ErrorHandler();
		$plugin->setErrorHandlerModule('ApiApp');
		Zend_Controller_Front::getInstance()->registerPlugin($plugin);
	}
	
	public function _routeShutdown_Layout()
	{
		$layout = \Zend_Layout::startMvc();
	
		$layout->disableLayout();
		
		Zend_Controller_Front::getInstance()->setParam('noViewRenderer', true);
	}

}