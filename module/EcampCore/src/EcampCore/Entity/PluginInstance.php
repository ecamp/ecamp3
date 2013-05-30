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

/**
 * This is the base class for both Panels and Plugins.
 * It shouldn't be extended by your own plugins - simply write a strategy!
 */

namespace EcampCore\Entity;

use EcampLib\Entity\BaseEntity;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

use Doctrine\ORM\Mapping as ORM;
use EcampCore\Acl\BelongsToParentResource;

/**
 * @ORM\Entity(repositoryClass="EcampCore\Repository\PluginInstanceRepository")
 * @ORM\Table(name="plugin_instances")
 */
class PluginInstance 
	extends BaseEntity
	implements  BelongsToParentResource
	,			ServiceLocatorAwareInterface
	
{
	
	public function __construct(ServiceLocatorInterface $serviceLocator){
		parent::__construct();
		
		$this->serviceLocator = $serviceLocator;
	}
	

	/**
	 * @ORM\ManyToOne(targetEntity="Event")
	 * @ORM\JoinColumn(nullable=false, onDelete="cascade")
	 * TODO: set to nullable=false later
	 */
	public $event;
	
	
	/**
	 * @var PluginPrototype
	 * @ORM\ManyToOne(targetEntity="PluginPrototype")
	 * @ORM\JoinColumn(nullable=false, onDelete="cascade")
	 * TODO change nullable to false
	 */
	protected $pluginPrototype;

	
	/**
	 * This var contains an instance of $this->pluginStrategy.
	 * The instance is loaded with a PostLoad event listener and will not be persisted by Doctrine.
	 *
	 * @var IPluginStrategy
	 */
	protected $strategyInstance;

	
	/**
	 * @var Zend\ServiceManager\ServiceLocatorInterface
	 */
	private $serviceLocator;

	
	
	public function setEvent(Event $event){
		$this->event = $event;
	}
	
	public function getEvent(){
		return $this->event;
	}
	
	public function getParentResource(){
		return $this->event;
	}
	
	
	public function getCamp(){
		return $this->event->getCamp();
	}
	
	/**
	 * Returns the plugin prototype
	 *
	 * @return PluginPrototype
	 */
	public function getPluginPrototype(){
	    return $this->pluginPrototype;
	}
	
	public function setPluginPrototype(PluginPrototype $pluginPrototype){
	    $this->pluginPrototype  = $pluginPrototype;
	}


	/**
	 * Returns the plugin name
	 *
	 * @return string
	 */
	public function getPluginName() {
		return $this->getPluginPrototype()->getPlugin()->getName();
	}

	/**
	 * Returns the strategy that is used for this pluginitem.
	 *
	 * The strategy itself defines how this plugin can be rendered etc.
	 *
	 * @return string
	 */
	public function getPluginStrategyClass() {
		return $this->getPluginPrototype()->getPlugin()->getStrategyClass();
	}

	
	public function setServiceLocator(ServiceLocatorInterface $serviceLocator){
		$this->serviceLocator = $serviceLocator;
	}

	public function getServiceLocator(){
		return $this->serviceLocator;
	}

	/**
	 * Returns the instantiated strategy
	 *
	 * @return EcampCore\Plugin\AbstractStrategy
	 */
	public function getStrategyInstance(){
		if($this->strategyInstance == null){
			$classname = $this->getPluginStrategyClass();
			$this->strategyInstance = new $classname($this->serviceLocator, $this);
		}
		return $this->strategyInstance;
	}
}