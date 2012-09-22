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

namespace CoreApi\Entity;

/**
 * EventPrototype
 * @Entity(readOnly=true)
 * @Table(name="event_prototypes")
 */
class EventPrototype extends BaseEntity
{

	public function __construct()
	{
	}

	/**
	 * @Column(type="string", length=64, nullable=false )
	 */
	private $name;
	
	/**
	 * If false, no more events of this prototype can be created.
	 * However, existing events will still be rendered.
	 * @Column(type="boolean", nullable=false)
	 */
	private $active = true;
	
	/**
	 * @OneToMany(targetEntity="PluginPrototype", mappedBy="prototype")
	 */
	private $pluginPrototypes;
	
	/**
	 * @OneToMany(targetEntity="EventTemplate", mappedBy="prototype")
	 */
	private $eventTemplates;
	
	
	public function setName($name)
	{
		$this->name = $name;
	}
	
	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}
	
	/**
	 * @return array
	 */
	public function getPluginPrototypes()
	{
		return $this->pluginPrototypes;
	}
	
	/**
	 * @return array
	 */
	public function getEventTemplates()
	{
	    return $this->eventTemplates;
	}
}
