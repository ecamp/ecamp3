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

    public function init()
    {
	    parent::init();

        if(!isset($this->me))
		{
			$this->_forward("index", "login");
			return;
		}
		
		/**
		 * @var \Entity\Camp
		 */
		$this->camp = null;
		
		/**
		 * @var \Entity\Group
		 */
		$this->group = null;
		
		
		 /* load camp */
	    $campid = $this->getRequest()->getParam("camp");
	    $this->camp = $this->em->getRepository("Core\Entity\Camp")->find($campid);
	    $this->view->camp = $this->camp;

	     /* load group */
	    $groupid = $this->getRequest()->getParam("group");
	    $this->group = $this->em->getRepository("Core\Entity\Group")->find($groupid);
	    $this->view->group = $this->group;
		
		 /* load user */
	    $userid = $this->getRequest()->getParam("user");
	    $this->user = $userid ? $this->em->getRepository("Core\Entity\User")->find($userid) : null;
	    $this->view->owner = $this->user;

	    $this->setNavigation(new \WebApp\Navigation\Camp($this->camp));

		
		/* move this to bootsrap */
		$event = new \WebApp\Plugin\StrategyEventListener($this->view, $this->em);
		$this->em->getEventManager()->addEventSubscriber($event);
	}


	/* list all events */
    public function indexAction()
    {
    }
	
	/* create an event */
    public function createAction()
    {
		$event = new Core\Entity\Event();
		$event->setCamp($this->camp);
		$event->setTitle(md5(time()));
		
		/* create header */
		$plugin = new \Core\Entity\Plugin();		
		$plugin->setEvent($event);
		$strategy = new \WebApp\Plugin\Header\Strategy($this->em, $this->view, $plugin);
		$plugin->setStrategy($strategy);
		$strategy->persist();
		$this->em->persist($plugin);

		/* create content */
		$plugin = new \Core\Entity\Plugin();
		$plugin->setEvent($event);
		$strategy = new \WebApp\Plugin\Content\Strategy($this->em, $this->view, $plugin);
		$plugin->setStrategy($strategy);
		$strategy->persist();
		$this->em->persist($plugin);

	    /* create content */
		$plugin = new \Core\Entity\Plugin();
		$plugin->setEvent($event);
		$strategy = new \WebApp\Plugin\Content\Strategy($this->em, $this->view, $plugin);
		$plugin->setStrategy($strategy);
		$strategy->persist();
		$this->em->persist($plugin);
		
		$this->em->persist($event);
		$this->em->flush();
		
		$this->_forward('index');
    }
	
	public function deleteAction(){
	
		$id = $this->getRequest()->getParam("id");
		$event = $this->em->getRepository("Core\Entity\Event")->find($id);
		
		foreach( $event->getPlugins() as $plugin )
		{
			$plugin->getStrategyInstance()->remove();
		}
		
		$this->em->remove($event);
		$this->em->flush();
		
		$this->_forward('index');
	}
	
	/* show an event (frontend, read only) */
	public function showAction()
	{
		$id = $this->getRequest()->getParam("id");
		
		if( !isset($id) )
			$this->_forward('index');
		
		$this->view->event = $this->em->getRepository("Core\Entity\Event")->find($id);
	}
	
	/* edit an event (backend, write access) */
	public function editAction()
	{	
		$id = $this->getRequest()->getParam("id");
		
		if( !isset($id) )
			$this->_forward('index');
		
		$this->view->event = $this->em->getRepository("Core\Entity\Event")->find($id);
	}

	/* call a function of the plugin */
	public function pluginAction(){

		$id = $this->getRequest()->getParam("id");
		$plugin = $this->em->getRepository("Core\Entity\Plugin")->find($id);

		Zend_Controller_Front::getInstance()->addControllerDirectory(
			APPLICATION_PATH . '/Module/WebApp/Plugin/'.$plugin->getStrategyInstance()->getPluginName().'/Controller', $plugin->getStrategyInstance()->getPluginName());

		/* convention: the plugins carries a param "pluginaction", which specifies the action of the plugin controller */
		$this->_forward( $this->getRequest()->getParam("pluginaction"), 'plugin', $plugin->getStrategyInstance()->getPluginName());
		
	}
	
}

