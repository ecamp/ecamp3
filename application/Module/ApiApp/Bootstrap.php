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
		
		
		
		///
		/// User
		///
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
		
		
		
		
		///
		/// Contributor
		///
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
		
		
		
		///
		/// Camp
		///
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
		
		Zend_Controller_Front::getInstance()->getRouter()->addRoute(
			'api.v1.group.camp.collection', $ApiSubdomain->chain(
				new Zend_Controller_Router_Route_Regex(
					'v1/groups/([0-9a-f]+)/camps\.(json|xml)',
					array('controller' => 'camp', 'action' => 'index'),
					array(1 => 'group', 2 => 'mime'),
					'v1/camps.%s'
				)
			)
		);
		
		
		
		///
		/// Period
		///
		Zend_Controller_Front::getInstance()->getRouter()->addRoute(
			'api.v1.period', $ApiSubdomain->chain(
				new Zend_Controller_Router_Route_Regex(
					'v1/periods/([0-9a-f]+)\.(json|xml)',
					array('controller' => 'period', 'action' => 'get'),
					array(1 => 'id', 2 => 'mime'),
					'v1/periods/%s.%s'
				)
			)
		);
				
		Zend_Controller_Front::getInstance()->getRouter()->addRoute(
			'api.v1.period.collection', $ApiSubdomain->chain(
				new Zend_Controller_Router_Route_Regex(
					'v1/periods\.(json|xml)',
					array('controller' => 'period', 'action' => 'index'),
					array(1 => 'mime'),
					'v1/periods.%s'
				)
			)
		);
		
		Zend_Controller_Front::getInstance()->getRouter()->addRoute(
			'api.v1.camp.period.collection', $ApiSubdomain->chain(
				new Zend_Controller_Router_Route_Regex(
					'v1/camps/([0-9a-f]+)/periods\.(json|xml)',
					array('controller' => 'period', 'action' => 'index'),
					array(1 => 'camp', 2 => 'mime'),
					'v1/camps/%s/periods.%s'
				)
			)
		);
		
		
		
		///
		/// Day
		///
		Zend_Controller_Front::getInstance()->getRouter()->addRoute(
			'api.v1.day', $ApiSubdomain->chain(
				new Zend_Controller_Router_Route_Regex(
					'v1/days/([0-9a-f]+)\.(json|xml)',
					array('controller' => 'day', 'action' => 'get'),
					array(1 => 'id', 2 => 'mime'),
					'v1/days/%s.%s'
				)
			)
		);
				
		Zend_Controller_Front::getInstance()->getRouter()->addRoute(
			'api.v1.day.collection', $ApiSubdomain->chain(
				new Zend_Controller_Router_Route_Regex(
					'v1/days\.(json|xml)',
					array('controller' => 'day', 'action' => 'index'),
					array(1 => 'mime'),
					'v1/days.%s'
				)
			)
		);
		
		Zend_Controller_Front::getInstance()->getRouter()->addRoute(
			'api.v1.period.day.collection', $ApiSubdomain->chain(
				new Zend_Controller_Router_Route_Regex(
					'v1/periods/([0-9a-f]+)/days\.(json|xml)',
					array('controller' => 'day', 'action' => 'index'),
					array(1 => 'period', 2 => 'mime'),
					'v1/periods/%s/days.%s'
				)
			)
		);
		
		
		///
		/// Event
		///
		Zend_Controller_Front::getInstance()->getRouter()->addRoute(
			'api.v1.event', $ApiSubdomain->chain(
				new Zend_Controller_Router_Route_Regex(
					'v1/events/([0-9a-f]+)\.(json|xml)',
					array('controller' => 'event', 'action' => 'get'),
					array(1 => 'id', 2 => 'mime'),
					'v1/events/%s.%s'
				)
			)
		);
						
		Zend_Controller_Front::getInstance()->getRouter()->addRoute(
			'api.v1.event.collection', $ApiSubdomain->chain(
				new Zend_Controller_Router_Route_Regex(
					'v1/events\.(json|xml)',
					array('controller' => 'event', 'action' => 'index'),
					array(1 => 'mime'),
					'v1/events.%s'
				)
			)
		);
		
		Zend_Controller_Front::getInstance()->getRouter()->addRoute(
			'api.v1.camp.event.collection', $ApiSubdomain->chain(
				new Zend_Controller_Router_Route_Regex(
					'v1/camps/([0-9a-f]+)/events\.(json|xml)',
					array('controller' => 'event', 'action' => 'index'),
					array(1 => 'camp', 2 => 'mime'),
					'v1/camps/%s/events.%s'
				)
			)
		);
		
		
		
		
		///
		/// EventInstance
		///
		Zend_Controller_Front::getInstance()->getRouter()->addRoute(
			'api.v1.eventinstance', $ApiSubdomain->chain(
				new Zend_Controller_Router_Route_Regex(
					'v1/eventinstances/([0-9a-f]+)\.(json|xml)',
					array('controller' => 'eventinstance', 'action' => 'get'),
					array(1 => 'id', 2 => 'mime'),
					'v1/eventinstances/%s.%s'
				)
			)
		);
								
		Zend_Controller_Front::getInstance()->getRouter()->addRoute(
			'api.v1.eventinstance.collection', $ApiSubdomain->chain(
				new Zend_Controller_Router_Route_Regex(
					'v1/eventinstances\.(json|xml)',
					array('controller' => 'eventinstance', 'action' => 'index'),
					array(1 => 'mime'),
					'v1/eventinstances.%s'
				)
			)
		);
		
		
		Zend_Controller_Front::getInstance()->getRouter()->addRoute(
			'api.v1.period.eventinstance.collection', $ApiSubdomain->chain(
			new Zend_Controller_Router_Route_Regex(
				'v1/periods/([0-9a-f]+)/eventinstances\.(json|xml)',
					array('controller' => 'eventinstance', 'action' => 'index'),
					array(1 => 'period', 2 => 'mime'),
					'v1/periods/%s/eventinstances.%s'
				)
			)
		);
		
		Zend_Controller_Front::getInstance()->getRouter()->addRoute(
			'api.v1.event.eventinstance.collection', $ApiSubdomain->chain(
			new Zend_Controller_Router_Route_Regex(
				'v1/events/([0-9a-f]+)/eventinstances\.(json|xml)',
					array('controller' => 'eventinstance', 'action' => 'index'),
					array(1 => 'event', 2 => 'mime'),
					'v1/events/%s/eventinstances.%s'
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
			$errorHandler, 'ApiApp', 'Error', 'error');
		
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