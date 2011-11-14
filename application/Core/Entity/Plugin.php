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

namespace Core\Entity;

/**
 * @Entity
 * @Table(name="plugins")
 */
class Plugin extends BaseEntity {
	/**
	 * The id of the plugin item instance
	 * @var integer
	 * @Id @Column(type="integer")
	 * @GeneratedValue(strategy="AUTO")
	 */
	private $id;
	
	/**
	 * @ManyToOne(targetEntity="Event")
	 * @JoinColumn(nullable=true, onDelete="cascade")
	 * TODO: set to nullable=false later
	 */
	public $event;

	/**
	 * This var contains the classname of the strategy
	 * that is used for this pluginitem. (This string (!) value will be persisted by Doctrine 2)
	 *
	 * @var string
	 * @Column(type="string", length=64, nullable=false )
	 */
	protected $strategyClassName;

	/**
	 * This var contains an instance of $this->pluginStrategy. Will not be persisted by Doctrine 2.
	 * The instance is loaded with a PostLoad event listener
	 *
	 * @var IPluginStrategy
	 */
	protected $strategyInstance;

	
	public function getId(){ return $this->id; }
	
	public function setEvent(Event $event){ $this->event = $event; }
	public function getEvent()            { return $this->event;   }
	
	
	/**
	 * Returns the strategy that is used for this pluginitem.
	 *
	 * The strategy itself defines how this plugin can be rendered etc.
	 *
	 * @return string
	 */
	public function getStrategyClassName() {
		return $this->strategyClassName;
	}

	/**
	 * Returns the instantiated strategy
	 *
	 * @return IPluginStrategy
	 */
	public function getStrategyInstance() {
		return $this->strategyInstance;
	}

	/**
	 * Sets the strategy this plugin / panel should work as. Make sure that you've used
	 * this method before persisting the plugin!
	 *
	 * @param IPluginStrategy $strategy
	 */
	public function setStrategy(\Core\Plugin\IPluginStrategy $strategy) {
		$this->strategyInstance  = $strategy;
		$this->strategyClassName = get_class($strategy);
		$strategy->setPlugin($this);
	}
}