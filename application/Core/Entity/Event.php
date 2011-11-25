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

namespace Core\Entity;

/**
 * Container for an event.
 * - An event has no date/time, as it only describes the program but not when it happens.
 * - An event can either belong to a camp or to a user
 * @Entity
 * @Table(name="events")
 */
class Event extends BaseEntity
{

	/**
	 * @return \CoreApi\Entity\Event
	 */
	public function asReadonly()
	{
		return new \CoreApi\Entity\Event($this);
	}


	/**
	 * @var int
	 * @Id @Column(type="integer")
	 * @GeneratedValue(strategy="AUTO")
	 */
	private $id;

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

	
	
	/** @Public:Method() */
	public function getId()
	{
		return $this->id;
	}

	public function setTitle($title)
	{
		$this->title = $title;
	}
	
	/** @Public:Method() */
	public function getTitle()
	{
		return $this->title;
	}

	public function setCamp(camp $camp)
	{
		$this->camp = $camp;
	}
	
	/**
	 * @Public:MethodEntity()
	 * @return \Core\Entity\Camp
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
	 * @Public:MethodEntity()
	 * @return \Core\Entity\User
	 */
	public function getUser()
	{
		return $this->user;
	}
	
	/**
	 * @Public:MethodEntityList(type = "\CoreApi\Entity\EventInstance")
	 * @return array
	 */
	public function getEventInstances()
	{
		return $this->eventInstances;
	}

	/**
	 * @Public:MethodEntityList(type = "\CoreApi\Entity\Plugin")
	 * @return array
	 */
	public function getPlugins()
	{
		return $this->plugins;
	}
}
