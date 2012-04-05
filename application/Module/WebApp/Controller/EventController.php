<?php
/*
 * Copyright (C) 2011 Urban Suppiger
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


class WebApp_EventController extends \WebApp\Controller\BaseController
{
	/**
	 * @var CoreApi\Service\EventService
	 * @Inject CoreApi\Service\EventService
	 */
	private $eventService;

    public function init()
    {
	    parent::init();

        if(!isset($this->me))
		{
			$this->_forward("index", "login");
			return;
		}
		
		/* load context */
		$context = $this->contextProvider->getContext();
	    $this->view->camp = $context->getCamp();
	    $this->view->group = $context->getGroup();
	    $this->view->owner = $context->getUser();

	    $this->setNavigation(new \WebApp\Navigation\Camp($context->getCamp()));

	}


	/* list all events */
    public function indexAction()
    {
    }
	
	/* create an event */
    public function createAction()
    {
    	$camp = $this->contextProvider->getContext()->getCamp();
    	
		$this->eventService->Create($camp, $this->view);
		
		$this->_forward('index');
    }
	
	public function deleteAction()
	{
		$id = $this->getRequest()->getParam("id");
		
		$this->eventService->Delete($id);

		$this->_forward('index');
	}
	
	/* show an event (frontend, read only) */
	public function showAction()
	{
		$id = $this->getRequest()->getParam("id");
		
		if( !isset($id) )
			$this->_forward('index');
		
		$this->view->event = $this->eventService->Get($id);
	}
	
	/* edit an event (backend, write access) */
	public function editAction()
	{	
		$id = $this->getRequest()->getParam("id");
		
		if( !isset($id) )
			$this->_forward('index');
		
		$this->view->event = $this->eventService->Get($id);
	}

	/* call a function of the plugin */
	public function pluginAction(){

		$id = $this->getRequest()->getParam("id");
		$plugin = $this->eventService->getPlugin($id);

		Zend_Controller_Front::getInstance()->addControllerDirectory(
			APPLICATION_PATH . '/Module/WebApp/Plugin/'.$plugin->getStrategyInstance()->getPluginName().'/Controller', $plugin->getStrategyInstance()->getPluginName());

		/* convention: the plugins carries a param "pluginaction", which specifies the action of the plugin controller */
		$this->_forward( $this->getRequest()->getParam("pluginaction"), 'plugin', $plugin->getStrategyInstance()->getPluginName());
		
	}
	
}

