<?php

namespace CoreApi\Service;


use Core\Acl\DefaultAcl;
use Core\Service\ServiceBase;

use CoreApi\Entity\Event;
use CoreApi\Entity\Camp;
use CoreApi\Entity\Plugin;
use CoreApi\Entity\PluginConfig;

use CoreApi\Entity\EventPrototype;


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
		$this->acl->allow(DefaultAcl::MEMBER, $this, 'GetContainers');
		$this->acl->allow(DefaultAcl::MEMBER, $this, 'getPlugin');
		$this->acl->allow(DefaultAcl::MEMBER, $this, 'AddPlugin');
		$this->acl->allow(DefaultAcl::MEMBER, $this, 'RemovePlugin');
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
	public function Create(Camp $camp)
	{	
		/* TODO: ugly workaround, find better solution please */
		$this->em->getConnection();
		
		/* define event prototype; will come as a parameter of course */
		$prototype = $this->em->getRepository("CoreApi\Entity\EventPrototype")->find(  1);
		
		$event = new Event();
		$event->setCamp($camp);
		$event->setTitle(md5(time()));
		$event->setPrototype($prototype);
		
		$pluginConfigs = $prototype->getConfigs();
		foreach($pluginConfigs as $config)
		{
		    for($i=0; $i<$config->getDefaultInstances(); $i++)
		    {
		        $this->CreatePlugin($event, $config);
		    }
		}
		
		$this->persist($event);
	}
	
	private function CreatePlugin(Event $event, PluginConfig $config)
	{
		$plugin = new Plugin();
		$plugin->setEvent($event);
		$plugin->setPluginConfig($config);
		
		$strategyClassName =  '\WebApp\Plugin\\' . $config->getPluginName() . '\Strategy';
		$strategy = new $strategyClassName($this->em, $plugin);
		$strategy->persist();
		
		$plugin->setStrategy($strategy);
		$this->persist($plugin);
		
		return $plugin;
	}
	
	public function GetContainers($id, $template)
	{   
		$event = $this->Get($id);
	    $mapitems =$template->getItems();
	    
	    $container = array();
	    foreach($mapitems as $item)
	    {
	    	if( !isset($container[$item->getContainer()] ) )
	    		$container[$item->getContainer()] = array();
	    	
	    	/* add all plugin instances to the container */
	    	$i=0;
	        foreach($event->getPluginsByConfig($item->getPluginConfig()) as $plugin)
	        {
	        	$citem = new ContainerItem();
	        	$citem->isPlugin = true;
	        	$citem->plugin = $plugin;
	        	$citem->config = $item->getPluginConfig();
	        	$citem->index  = $i;
	        	
	            $container[$item->getContainer()][] = $citem;
	            $i++; 
	        }
	        
	        /* add placeholder if max number of plugin instances has not been reached */
	        if( is_null($item->getPluginConfig()->getMaxInstances()) || $i < $item->getPluginConfig()->getMaxInstances() )
	        {
	        	$citem = new ContainerItem();
	        	$citem->isPlaceholder = true;
	        	$citem->config = $item->getPluginConfig();
	        	$container[$item->getContainer()][] = $citem;
	        }
	        
	    }
	    
	    return $container;
	}
	
	public function AddPlugin($event, $config)
	{
		$event = $this->Get($event);
		$config = $this->getPluginConfig($config);
		$count = $event->countPluginsByConfig($config);
		
		if( is_null($config->getMaxInstances()) || $count<$config->getMaxInstances() )
		{
			$this->CreatePlugin($event, $config);
		}
	}
	
	public function RemovePlugin($event, $instance)
	{
		$event 	  = $this->Get($event);
		$instance = $this->getPlugin($instance);
		$config = $instance->getPluginConfig();
		
		$count = $event->countPluginsByConfig($config);
		
		if( $count > $config->getMinInstances() )
		{
			/* cleanup plugin data */
			$instance->getStrategyInstance()->remove();
			
			/* remove plugin record itself */
			$this->remove($instance);
		}
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
	
	/**
	 * @return CoreApi\Entity\Plugin
	 */
	public function getPluginConfig($id)
	{
		if(is_numeric($id))
		{
			return $this->em->getRepository("\CoreApi\Entity\PluginConfig")->find($id);
		}
			
		if($id instanceof PluginConfig)
		{
			return $id;
		}
	
		return null;
	}
}

/**
 * only used to support output of container
 */
class ContainerItem
{
	/**
	 * @var boolean
	 */
	public $isPlugin = false;

	/**
	 * @var boolean
	 */
	public $isPlaceholder = false;

	/**
	 * @var \Core\Plugin\AbstractStrategy
	 */
	public $plugin;

	/**
	 * @var \CoreApi\Entity\PluginConfig
	 */
	public $config;

	/**
	 * internal number of this plugin instance
	 * @var integer
	 */
	public $index;

	public function isDeletable()
	{
		return $this->index > $this->config->getMinInstances();
	}
}