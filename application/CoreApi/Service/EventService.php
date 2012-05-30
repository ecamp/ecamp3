<?php

namespace CoreApi\Service;


use Core\Acl\DefaultAcl;
use Core\Service\ServiceBase;

use CoreApi\Entity\Event;
use CoreApi\Entity\Camp;
use CoreApi\Entity\Plugin;


/**
 * @method CoreApi\Service\EventService Simulate
 */
class EventService
	extends ServiceBase
{
	
	/**
	 * Setup ACL
	 * @return void
	 */
	public function _setupAcl()
	{
		$this->acl->allow(DefaultAcl::MEMBER, $this, 'Create');
		$this->acl->allow(DefaultAcl::MEMBER, $this, 'Delete');
		$this->acl->allow(DefaultAcl::MEMBER, $this, 'Get');
		$this->acl->allow(DefaultAcl::MEMBER, $this, 'getPlugin');
	}
	
	/**
	 * @return CoreApi\Entity\Event | NULL
	 */
	public function Get($id)
	{	
		if(is_numeric($id))
		{	return $this->em->getRepository("\CoreApi\Entity\Event")->find($id);	}
			
		if($id instanceof Event)
		{	return $id;	}
		
		return null;
	}
	
	/**
	 * @return bool
	 */
	public function Delete($id)
	{
		$event = $this->Get($id);
		
		foreach( $event->getPlugins() as $plugin )
		{
			$plugin->getStrategyInstance()->remove();
		}
		
		$this->em->remove($event);
		
		return true;
	}
	
	/**
	 * @return CoreApi\Entity\Event
	 */
	public function Create(Camp $camp, \Zend_View_Interface $view)
	{	
		/* TODO: ugly workaround, find better solution please */
		$this->em->getConnection();
		
		$event = new Event();
		$event->setCamp($camp);
		$event->setTitle(md5(time()));
		
		/* create header */
		$plugin = new Plugin();
		$plugin->setEvent($event);
		$strategy = new \WebApp\Plugin\Header\Strategy($this->em, $view, $plugin);
		$plugin->setStrategy($strategy);
		$strategy->persist();
		$this->em->persist($plugin);
		
		/* create content */
		$plugin = new Plugin();
		$plugin->setEvent($event);
		$strategy = new \WebApp\Plugin\Content\Strategy($this->em, $view, $plugin);
		$plugin->setStrategy($strategy);
		$strategy->persist();
		$this->em->persist($plugin);
		
		/* create content */
		$plugin = new Plugin();
		$plugin->setEvent($event);
		$strategy = new \WebApp\Plugin\Content\Strategy($this->em, $view, $plugin);
		$plugin->setStrategy($strategy);
		$strategy->persist();
		$this->em->persist($plugin);
		
		$this->em->persist($event);
	}
	
	/**
	 * @return CoreApi\Entity\Plugin
	 */
	public function getPlugin($id)
	{
		if(is_numeric($id))
		{
			return $this->em->getRepository("\CoreApi\Entity\Plugin")->find($id);
		}
			
		if($id instanceof Plugin)
		{
			return $id;
		}
		
		return null;
	}
	
}