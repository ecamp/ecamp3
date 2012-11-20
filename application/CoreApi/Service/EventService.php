<?php

namespace CoreApi\Service;


use CoreApi\Entity\Medium;

use Core\Plugin\RenderPluginInstance;
use Core\Plugin\RenderContainer;
use Core\Plugin\RenderPluginPrototype;
use Core\Plugin\RenderEvent;

use Core\Acl\DefaultAcl;
use Core\Service\ServiceBase;

use CoreApi\Entity\Event;
use CoreApi\Entity\Camp;
use CoreApi\Entity\Plugin;
use CoreApi\Entity\PluginInstance;

use CoreApi\Entity\EventPrototype;
use CoreApi\Entity\PluginPrototype;


/**
 * @method CoreApi\Service\EventService Simulate
 */
class EventService
	extends ServiceBase
{
	
	/**
	 * @var Core\Repository\EventTemplateRepository
	 * @Inject Core\Repository\EventTemplateRepository
	 */
	private $eventTemplateRepo;
	
	/**
	 * Setup ACL
	 * @return void
	 */
	public function _setupAcl()
	{
		$this->acl->allow(DefaultAcl::MEMBER, $this, 'Create');
		$this->acl->allow(DefaultAcl::MEMBER, $this, 'Delete');
		$this->acl->allow(DefaultAcl::MEMBER, $this, 'Get');
		$this->acl->allow(DefaultAcl::MEMBER, $this, 'GetContainers');
		$this->acl->allow(DefaultAcl::MEMBER, $this, 'CreateRenderEvent');
		$this->acl->allow(DefaultAcl::MEMBER, $this, 'getPluginInstance');
		$this->acl->allow(DefaultAcl::MEMBER, $this, 'getPluginPrototype');
		$this->acl->allow(DefaultAcl::MEMBER, $this, 'AddPlugin');
		$this->acl->allow(DefaultAcl::MEMBER, $this, 'RemovePlugin');
		$this->acl->allow(DefaultAcl::GUEST, $this, 'getCampOfPluginInstance');
		
	}
	
	/**
	 * @return CoreApi\Entity\Event | NULL
	 */
	public function Get($id)
	{	
		if(is_string($id))
		{	return $this->em->getRepository("CoreApi\Entity\Event")->find($id);	}
			
		if($id instanceof Event)
		{	return $id;	}
		
		return null;
	}
	
	
	public function CreateRenderEvent(Event $event, $medium, $backend = false)
	{
		if(! $medium instanceof Medium )
		{
			$medium = $this->em->getRepository('CoreApi\Entity\Medium')->findOneBy(array('name' => $medium));	
		}
		
		$eventPrototype = $event->getPrototype();
		$eventTemplate = $this->eventTemplateRepo->findOneBy(
			array('eventPrototype' => $eventPrototype, 'medium' => $medium)); 
		
		$renderEvent = new RenderEvent($event, $medium, $eventTemplate, $backend);
		$renderContainers = array();
		
		$pluginPositions = $eventTemplate->getPluginPositions();
		foreach($pluginPositions as $pluginPosition){
			
			$containerName = $pluginPosition->getContainer();
			if(! array_key_exists($containerName, $renderContainers)){
				$renderContainers[$containerName] = new RenderContainer($renderEvent, $containerName);
			}
			$renderContainer = $renderContainers[$containerName];
			$pluginPrototype = $pluginPosition->getPluginPrototype();
			
			$renderPluginPrototype = new RenderPluginPrototype($renderContainer, $pluginPrototype);
			
			
			$pluginInstances = $this->em->getRepository('CoreApi\Entity\PluginInstance')->findBy(
				array('event' => $event, 'pluginPrototype' => $pluginPrototype));
			
			foreach($pluginInstances as $pluginInstance){
				new RenderPluginInstance($renderPluginPrototype, $pluginInstance);
			}
		}
		
		return $renderEvent;
	}
	
	
	/**
	 * @return bool
	 */
	public function Delete($id)
	{
		$event = $this->Get($id);
		
		foreach( $event->getPluginInstances() as $plugin )
		{
			$plugin->getStrategyInstance()->remove();
		}
		
		$this->em->remove($event);
		
		return true;
	}
	
	/**
	 * @return CoreApi\Entity\Event
	 */
	public function Create(Camp $camp)
	{	
		/* TODO: ugly workaround, find better solution please */
		$this->em->getConnection();
		
		/* define event prototype; will come as a parameter of course */
		$prototype = $this->em->getRepository("CoreApi\Entity\EventPrototype")->find(1);
	
		$event = new Event();
		
		$event->setCamp($camp);
		$event->setTitle(md5(time()));
		$event->setPrototype($prototype);

		$pluginPrototypes = $prototype->getPluginPrototypes();
		foreach($pluginPrototypes as $plugin)
		{
			for($i=0; $i<$plugin->getDefaultInstances(); $i++)
		    {
		        $this->CreatePluginInstance($event, $plugin);
		    }
		}

		$this->persist($event);
	}
	
	private function CreatePluginInstance(Event $event, PluginPrototype $prototype)
	{
		$plugin = new PluginInstance();
		$plugin->setEvent($event);
		$plugin->setPluginPrototype($prototype);
		
		$strategyClassName =  '\Plugin\\' . $prototype->getPlugin()->getName() . '\Strategy';
		$strategy = new $strategyClassName($this->em, $plugin);
		$strategy->persist();
		
		$plugin->setStrategy($strategy);
		$this->persist($plugin);
		
		return $plugin;
	}
	
	public function AddPlugin($event, $plugin)
	{
		$event = $this->Get($event);
		$plugin = $this->getPluginPrototype($plugin);
		$count = $event->countPluginsByPrototype($plugin);
		
		if( is_null($plugin->getMaxInstances()) || $count<$plugin->getMaxInstances() )
		{
			$this->CreatePluginInstance($event, $plugin);
		}
	}
	
	public function RemovePlugin($event, $instance)
	{
		$event 	  = $this->Get($event);
		$instance = $this->getPluginInstance($instance);
		$prototype = $instance->getPluginPrototype();
		
		$count = $event->countPluginsByPrototype($prototype);
		
		if( $count > $prototype->getMinInstances() )
		{
			/* cleanup plugin data */
			$instance->getStrategyInstance()->remove();
			
			/* remove plugin record itself */
			$this->remove($instance);
		}
	}
	
	/**
	 * @return CoreApi\Entity\PluginInstance
	 */
	public function getPluginInstance($id)
	{
		if(is_string($id))
		{
			return $this->em->getRepository("\CoreApi\Entity\PluginInstance")->find($id);
		}
			
		if($id instanceof PluginInstance)
		{
			return $id;
		}
		
		return null;
	}
	
	/**
	 * @return CoreApi\Entity\Plugin
	 */
	public function getPluginPrototype($id)
	{
		if(is_string($id))
		{
			return $this->em->getRepository("\CoreApi\Entity\PluginPrototype")->find($id);
		}
			
		if($id instanceof PluginPrototype)
		{
			return $id;
		}
	
		return null;
	}
	
	public function getCampOfPluginInstance($id)
	{
		$instance = $this->em->getRepository("\CoreApi\Entity\PluginInstance")->find($id);
		
		return $instance->getEvent()->getCamp();
	}
	
}