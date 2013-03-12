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
					array('controller' => 'plugin', 'action' => 'index'), 
					array('id' => '[0-9a-f]+')
				)
			)
		);
		
		
		
		Zend_Controller_Front::getInstance()->getRouter()->addRoute(
			'api.v1.user', $ApiSubdomain->chain(
				new Zend_Controller_Router_Route_Regex(
					'v1/users/([0-9a-f]+)\.(json|xml)',
					array('controller' => 'user', 'action' => 'get'),
					array(1 => 'id', 2 => 'mime'),
					'v1/users/%s.%s'
				)
			)
		);
		
		Zend_Controller_Front::getInstance()->getRouter()->addRoute(
			'api.v1.user.collection', $ApiSubdomain->chain(
				new Zend_Controller_Router_Route_Regex(
					'v1/users\.(json|xml)',
					array('controller' => 'user', 'action' => 'index'),
					array(1 => 'mime'),
					'v1/users.%s'
				)
			)
		);
		
		
		
		
		Zend_Controller_Front::getInstance()->getRouter()->addRoute(
			'api.v1.contributor', $ApiSubdomain->chain(
				new Zend_Controller_Router_Route_Regex(
					'v1/contributors/([0-9a-f]+)\.(json|xml)',
					array('controller' => 'contributor', 'action' => 'get'),
					array(1 => 'id', 2 => 'mime'),
					'v1/contributors/%s.%s'
				)
			)
		);
		
		Zend_Controller_Front::getInstance()->getRouter()->addRoute(
			'api.v1.contributor.collection', $ApiSubdomain->chain(
				new Zend_Controller_Router_Route_Regex(
					'v1/contributors\.(json|xml)',
					array('controller' => 'contributor', 'action' => 'index'),
					array(1 => 'mime'),
					'v1/contributors.%s'
				)
			)
		);
		
		Zend_Controller_Front::getInstance()->getRouter()->addRoute(
			'api.v1.camp.contributor.collection', $ApiSubdomain->chain(
				new Zend_Controller_Router_Route_Regex(
					'v1/camps/([0-9a-f]+)/contributors\.(json|xml)',
					array('controller' => 'contributor', 'action' => 'index'),
					array(1 => 'camp', 2 => 'mime'),
					'v1/camps/%s/contributors.%s'
				)
			)
		);
		
		
		
		Zend_Controller_Front::getInstance()->getRouter()->addRoute(
			'api.v1.camp', $ApiSubdomain->chain(
				new Zend_Controller_Router_Route_Regex(
					'v1/camps/([0-9a-f]+)\.(json|xml)',
					array('controller' => 'camp', 'action' => 'get'),
					array(1 => 'id', 2 => 'mime'),
					'v1/camps/%s.%s'
				)
			)
		);
				
		Zend_Controller_Front::getInstance()->getRouter()->addRoute(
			'api.v1.camp.collection', $ApiSubdomain->chain(
				new Zend_Controller_Router_Route_Regex(
					'v1/camps\.(json|xml)',
					array('controller' => 'camp', 'action' => 'index'),
					array(1 => 'mime'),
					'v1/camps.%s'
				)
			)
		);
		
		
		
		// http://api.ecamp3.ch/v1/camps/cid1/contributors.json 
		
		
// 		new Zend_Rest_Route(Zend_Controller_Front::getInstance(),
// 			array(),
// 			array('ApiApp')
// 		);
		
// 		Zend_Controller_Front::getInstance()->getRouter()->addRoute(
// 			'test', $ApiSubdomain->chain(
				
// 			)
// 		);
		
		
		/* General Router */
// 		Zend_Controller_Front::getInstance()->getRouter()->addRoute(
// 			'general', $ApiSubdomain->chain(
// 				new Zend_Controller_Router_Route('/:controller',
// 					array('controller' => 'error', 'action' => 'index')
// 				)
// 			)
// 		);
		
		/* General+Id Router */
// 		Zend_Controller_Front::getInstance()->getRouter()->addRoute(
// 			'general+id', $ApiSubdomain->chain(
// 				new Zend_Controller_Router_Route('/:controller/:id',
// 					array('controller' => 'error', 'action' => 'get'), 
// 					array('id' => '[0-9a-f]+')
// 				)
// 			)
// 		);
				
	}
	
	
	/**
	 * Load and configure error handler
	 */
	public function _initErrorHandler()
	{
		$errorHandler = Zend_Registry::get('errorHandler');
		
		$plugin = new Core\Error\ConfigureErrorHandler(
			$errorHandler, 'ApiApp', 'error', 'error');
		
		Zend_Controller_Front::getInstance()->registerPlugin($plugin);
	}
	
	
	public function _routeShutdown_Layout()
	{
		$layout = \Zend_Layout::startMvc();
	
		$layout->disableLayout();
		
		Zend_Controller_Front::getInstance()->setParam('noViewRenderer', true);
	}
	
	/*
	public function _routeShutdown_ErrorHandler(){
		$this->configErrorHandler();
	}
	
	public function _preDispatch_ErrorHandler(){
		$this->configErrorHandler();
	}
	
	
	private static $apiErrorHandler = null;
	
	private function configErrorHandler(){
		
		if(self::$apiErrorHandler == null){
			self::$apiErrorHandler = new Zend_Controller_Plugin_ErrorHandler();
			self::$apiErrorHandler->setErrorHandlerModule('ApiApp');
			self::$apiErrorHandler->setErrorHandlerController('Error');
			self::$apiErrorHandler->setErrorHandlerAction('error');
		}
		
		$errorHandler = Zend_Registry::get('errorHandler');
		$errorHandler->setErrorHandler(self::$apiErrorHandler);
	}
	*/
}