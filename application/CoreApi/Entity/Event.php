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

namespace CoreApi\Entity;

/**
 * Container for an event.
 * - An event has no date/time, as it only describes the program but not when it happens.
 * - An event can either belong to a camp or to a user
 * @Entity
 * @Table(name="events")
 */
class Event extends BaseEntity
{
    public function __construct()
    {
        $this->plugins  = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
	/**
	 * @Column(type="text" )
	 */
	private $title;

	/**
	 * @ManyToOne(targetEntity="Camp")
	 */
	private $camp;

	/**
	 * @ManyToOne(targetEntity="User")
	 */
	private $user;

	/**
	 * @OneToMany(targetEntity="EventInstance", mappedBy="event", cascade={"all"}, orphanRemoval=true)
	 */
	private $eventInstances;

	/**
	 * @OneToMany(targetEntity="Plugin", mappedBy="event", cascade={"all"}, orphanRemoval=true)
	 */
	private $plugins;
	
	/**
	 * @var EventPrototype
	 * @ManyToOne(targetEntity="EventPrototype")
	 * @JoinColumn(nullable=true, onDelete="cascade")
	 * @TODO set nullable false
	 */
	private $prototype;
	
	
	public function setTitle($title)
	{
		$this->title = $title;
	}
	
	public function getTitle()
	{
		return $this->title;
	}

	
	public function setCamp(camp $camp)
	{
		$this->camp = $camp;
	}
	
	/**
	 * @return Camp
	 */
	public function getCamp()
	{
		return $this->camp;
	}

	
	public function setUser(user $user)
	{
		$this->user = $user;
	}
	
	/**
	 * @return User
	 */
	public function getUser()
	{
		return $this->user;
	}
	
	
	/**
	 * @return array
	 */
	public function getEventInstances()
	{
		return $this->eventInstances;
	}

	
	/**
	 * @return array
	 */
	public function getPlugins()
	{
		return $this->plugins;
	}
	
	/**
	 * @return array
	 */
	public function getPluginsByConfig(PluginConfig $config)
	{
	    $closure = function(Plugin $plugin) use ($config)
	    {
	        return $plugin->getPluginConfig()->getId() == $config->getId();
	    };
	    
	    return $this->plugins->filter($closure);
	}
	
	/**
	 * @return integer
	 */
	public function countPluginsByConfig(PluginConfig $config)
	{
		$closure = function(Plugin $plugin) use ($config)
		{
			return $plugin->getPluginConfig()->getId() == $config->getId();
		};
		 
		return $this->plugins->count($closure);
	}
	
	/**
	 * @return EventPrototype
	 */
	public function getPrototype()
	{
	    return $this->prototype;
	}
	
	public function setPrototype(EventPrototype $prototype)
	{
	    $this->prototype = $prototype;
	}
}
