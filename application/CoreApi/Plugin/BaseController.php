<?php
/*
 * Copyright (C) 2011 Urban Suppiger
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
 
namespace CoreApi\Plugin;

class BaseController extends \Zend_Controller_Action
{
	
	/**
	 * @var Doctrine\ORM\EntityManager
	 * @Inject Doctrine\ORM\EntityManager
	 */
	protected $em;

	/**
	 * @var CoreApi\Service\EventService
	 * @Inject CoreApi\Service\EventService
	 */
	protected $eventService;

	
	
	/**
	 * logged in user
	 * @var CoreApi\Entity\User
	 */
	protected $me;
	
	/**
	 * default translator
	 * @var \Zend_Translate
	 */
	protected $t;
	
	/**
	 * @var CoreApi\Acl\ContextProvider
	 * @Inject CoreApi\Acl\ContextProvider
	 */
	protected $contextProvider;
	
	/**
	 * @var Core\Acl\DefaultAcl
	 * @Inject Core\Acl\DefaultAcl
	 */
	protected $acl;
	
	
	public function getContext(){
		return $this->contextProvider->getContext();
	}
	
	public function init()
	{
		\Zend_Registry::get('kernel')->Inject($this);
	
	
		/* load translator */
		$this->t = new \Zend_View_Helper_Translate();
	
		/* register events */
		$em = \Zend_Registry::get('kernel')->Get("Doctrine\ORM\EntityManager");
		
		$event = new \WebApp\Plugin\StrategyEventListener($em);
		$em->getEventManager()->addEventSubscriber($event);
	}
	
	
	protected function forward($route, $action, $controller = null, $module = null, array $params = null)
	{
		$request = $this->getRequest();
		$request->clearParams();
		
		if (null !== $params) {
			$request->setParams($params);
		}
	
		if (null !== $controller) {
			$request->setControllerName($controller);
			$request->setParam($request->getControllerKey(), $controller);
		
			// Module should only be reset if controller has been specified
			if (null !== $module) {
				$request->setModuleName($module);
				$request->setParam($request->getModuleKey(), $module);
			}
		}
	
		$request->setActionName($action);
		$request->setParam($request->getActionKey(), $action);
		$request->setDispatched(false);
	

		$router = \Zend_Controller_Front::getInstance()->getRouter();
		$url = $router->assemble($request->getParams(), $route, true);

		$this->view->BrowserUrlScript =
		"<script type='text/javascript'> window.history.replaceState({}, '', '$url'); </script>";
	}
}
