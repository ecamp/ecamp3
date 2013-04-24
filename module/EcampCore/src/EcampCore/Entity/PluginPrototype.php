<?php
/*
 * Copyright (C) 2012 Urban Suppiger
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

namespace EcampCore\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PluginPrototype
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="plugin_prototypes")
 *  //,  uniqueConstraints={@ORM\UniqueConstraint(name="prototype_plugin_unique",columns={"prototype_id", "pluginName"})})
 */
class PluginPrototype extends BaseEntity
{

    public function __construct($eventPrototype = null){
        $this->eventPrototype = $eventPrototype;
    }

	/**
	 * @var Plugin
	 * @ORM\ManyToOne(targetEntity="Plugin")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $plugin;
	
	/**
	 * @var EventPrototype
	 * @ORM\ManyToOne(targetEntity="EventPrototype")
	 * @ORM\JoinColumn(nullable=false, onDelete="cascade")
	 */
	private $eventPrototype;
	
	/**
	 * If false, no more plugin instances can be created (also default instances are not created anymore).
	 * However, existing plugins
	 * @ORM\Column(type="boolean", nullable=false) 
	 */
	private $active = true;
	
	/**
	 * Maximum number of plugin instances  per event (null=unlimited)
	 * @ORM\Column(type="smallint", nullable=true)
	 */
	private $maxInstances = 1;
	
	/**
	 * Number of plugin instances that are created by default for a new event
	 * @ORM\Column(type="smallint", nullable=false)
	 */
	private $defaultInstances = 1;
	
	/**
	 * Minimum number of plugin instances per event. Settings this value>0 makes those instances non-deletable.
	 * @ORM\Column(type="smallint", nullable=false)
	 */
	private $minInstances  = 0;
	
	/**
	 * Further configuration of this plugin in JSON format
	 *
	 * @var string
	 * @ORM\Column(type="text" )
	 */
	private $config;
	
	
	/**
	 * @return Plugin
	 */
	public function getPlugin(){
	    return $this->plugin;
	}
	
	
	public function setConfig($config){
	    $this->config = json_encode($config);
	}
	
	/**
	 * @return string
	 */
	public function getConfig(){
		return json_decode($this->config);
	}
	
	/**
	 * @return integer
	 */
	public function getDefaultInstances(){
	    return $this->defaultInstances;
	}
	
	/**
	 * @return integer
	 */
	public function getMaxInstances(){
	    return $this->maxInstances;
	}
	
	/**
	 * @return integer
	 */
	public function getMinInstances(){
	    return $this->minInstances;
	}
	
}
