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

class ApiApp_PluginController extends \Zend_Controller_Action
{	
	
	/**
	 * @var PhpDI\IKernel
	 * @Inject PhpDI\IKernel
	 */
	private $kernel;
	
	/**
	 * @var CoreApi\Service\EventService
	 * @Inject CoreApi\Service\EventService
	 */
	private $eventService;
	
	/**
	 * @var CoreApi\Acl\ContextProvider
	 * @Inject CoreApi\Acl\ContextProvider
	 */
	protected $contextProvider;
	
	/**
	 * @var Doctrine\ORM\EntityManager
	 * @Inject Doctrine\ORM\EntityManager
	 */
	protected $em;
	
    public function init()
    {
		parent::init();
		
		\Zend_Registry::get('kernel')->Inject($this);
		
		$this->getResponse()->setHeader('Content-Type', 'text/plain');
		
		$this->getResponse()->setHeader('Access-Control-Allow-Origin', 'http://www.ecamp3.dev');
		$this->getResponse()->setHeader('Access-Control-Allow-Credentials', 'true');
	//	$this->getResponse()->setHeader('Access-Control-Allow-Methods', 'GET,PUT,POST,DELETE,OPTIONS');
	//	$this->getResponse()->setHeader('Access-Control-Allow-Headers', 'X-CSRF-Token, X-Requested-With, Accept, Accept-Version, Content-Length, Content-MD5, Content-Type, Date, X-Api-Version');
		
    }
    
    public function indexAction()
    {
    	$id = $this->getRequest()->getParam("id");
    	$method = $this->getRequest()->getParam("method");
    	
    	$this->contextProvider->set(null, null, $this->eventService->getCampOfPluginInstance($id)->getId());

    	/* load instance */
    	$pluginInstance = $this->eventService->getPluginInstance($id);
    	$prototype = $pluginInstance->getPluginPrototype();
    	$event = $pluginInstance->getEvent();
    	$camp = $event->getCamp();
    	
    	$serviceClass      = $prototype->getServiceClassName();
    	$pluginService = new $serviceClass($pluginInstance);
   
    	$this->kernel->Inject($pluginService);
    	
    	$response = array();
    	$response["response"] = $pluginService->$method($this->getRequest()->getParams());
    	$response["request"] = $this->getRequest()->getParams();
    	
    	$this->em->flush();
    	
    	$this->getResponse()->setBody( Zend_Json::encode($response) );
    }
}

