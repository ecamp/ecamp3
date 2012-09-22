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

namespace CoreApi\Entity;

/**
 * @Entity
 * @Table(name="plugin_instances")
 */
class PluginInstance extends BaseEntity
{

	/**
	 * @ManyToOne(targetEntity="Event")
	 * @JoinColumn(nullable=true, onDelete="cascade")
	 * TODO: set to nullable=false later
	 */
	public $event;
	
	
	/**
	 * @var PluginPrototype
	 * @ManyToOne(targetEntity="PluginPrototype")
	 * @JoinColumn(nullable=true, onDelete="cascade")
	 * @TODO change nullable to false
	 */
	protected $pluginPrototype;

	
	/**
	 * This var contains an instance of $this->pluginStrategy.
	 * The instance is loaded with a PostLoad event listener and will not be persisted by Doctrine.
	 *
	 * @var IPluginStrategy
	 */
	protected $strategyInstance;


	
	public function setEvent(Event $event)
	{
		$this->event = $event;
	}
	
	public function getEvent()
	{
		return $this->event;
	}


	/**
	 * Returns the plugin name
	 *
	 * @return string
	 */
	public function getPluginName() 
	{
		return $this->pluginPrototype->getPlugin()->getName();
	}
	
	/**
	 * Returns the plugin config
	 *
	 * @return PluginPrototype
	 */
	public function getPluginPrototype()
	{
	    return $this->pluginPrototype;
	}
	
	public function setPluginConfig(PluginPrototype $pluginPrototype)
	{
	    $this->pluginPrototype  = $pluginPrototype;
	}

	/**
	 * Returns the strategy that is used for this pluginitem.
	 *
	 * The strategy itself defines how this plugin can be rendered etc.
	 *
	 * @return string
	 */
	public function getStrategyClassName() 
	{
		return '\\Plugin\\' . $this->getPluginName() . '\\Strategy';
	}



	/**
	 * Returns the instantiated strategy
	 *
	 * @return IPluginStrategy
	 */
	public function getStrategyInstance()
	{
		return $this->strategyInstance;
	}

	/**
	 * Sets the strategy this plugin / panel should work as. Make sure that you've used
	 * this method before persisting the plugin.
	 *
	 * @param IPluginStrategy $strategy
	 */
	public function setStrategy(\Core\Plugin\IPluginStrategy $strategy) 
	{
		$this->strategyInstance  = $strategy;

		$strategy->setPlugin($this);
	}
}