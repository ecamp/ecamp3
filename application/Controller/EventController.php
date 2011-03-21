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


class EventController extends \Controller\BaseController
{

    public function init()
    {
	    parent::init();

        if(!isset($this->me))
		{
			$this->_forward("index", "login");
			return;
		}
		
		 /* load camp */
	    $campid = $this->getRequest()->getParam("camp");
	    $this->camp = $this->em->getRepository("Entity\Camp")->find($campid);
	    $this->view->camp = $this->camp;

	     /* load group */
	    $groupid = $this->getRequest()->getParam("group");
	    $this->group = $this->em->getRepository("Entity\Group")->find($groupid);
	    $this->view->group = $this->group;
		
	    $pages = array(
			array(
			'label'      => 'General',
			'title'      => 'General',
			'controller' => 'camp',
			'action'     => 'show',
			'route'		 => 'group+camp'),

		    array(
			'label'      => 'Events',
			'title'      => 'Events',
			'controller' => 'event',
			'action'     => 'index',
			'route'		 => 'group+camp'),

		    array(
			'label'      => 'Print',
			'title'      => 'Print',
			'controller' => 'camp',
			'action'     => 'print',
			'route'		 => 'group+camp')
	    );

	    $container = new Zend_Navigation($pages);
		$this->view->getHelper('navigation')->setContainer($container);

	    /* inject group id into navigation */
	    foreach($container->getPages() as $page){
			$page->setParams(array(
				'camp' => $this->camp->getId(),
				'group' => $this->group->getId()
			));
		}
		
		/* move this to bootsrap */
		$event = new \Plugin\StrategyEventListener($this->view, $this->em);
		$this->em->getEventManager()->addEventSubscriber($event);
	}


	/* list all events */
    public function indexAction()
    {
    }
	
	/* create an event */
    public function createAction()
    {
		$event = new Entity\Event();
		$event->setCamp($this->camp);
		$event->setTitle(md5(time()));
		
		/* create plugins */
		$plugin = new \Entity\Plugin();		
		$plugin->setEvent($event);
		$headerstrategy = new \Plugin\HeaderStrategy($this->em, $this->view, $plugin);
		$plugin->setStrategy($headerstrategy);
		$headerstrategy->persist();
		$this->em->persist($plugin);
		
		$this->em->persist($event);
		$this->em->flush();
		
		$this->_forward('index');
    }
	
	public function deleteAction(){
	
		$id = $this->getRequest()->getParam("id");
		$event = $this->em->getRepository("Entity\Event")->find($id);
		
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
		
		$this->view->event = $this->em->getRepository("Entity\Event")->find($id);
	}
	
	/* edit an event (backend, write access) */
	public function editAction()
	{	
		$id = $this->getRequest()->getParam("id");
		
		if( !isset($id) )
			$this->_forward('index');
		
		$this->view->event = $this->em->getRepository("Entity\Event")->find($id);
	}

	/* call a function of the plugin */
	public function pluginAction(){

		$id = $this->getRequest()->getParam("id");
		$plugin = $this->em->getRepository("Entity\Plugin")->find($id);
		
		/* convention: the plugins carries a param "pluginaction", which specifies the action of the plugin controller */
		$this->_forward( $this->getRequest()->getParam("pluginaction"), $plugin->getStrategyInstance()->controller );
		
	}
	
}

