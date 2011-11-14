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
	
	public function getId(){ return $this->id; }

	public function setTitle($title){ $this->title = $title; }
	public function getTitle()            { return $this->title;   }
	
	public function setCamp(camp $camp){ $this->camp = $camp; }
	public function getCamp()          { return $this->camp; }
	
	public function setUser(user $user){ $this->user = $user; }
	public function getUser()          { return $this->user; }
	
	public function getEventInstances() { return $this->eventInstances; }
	
	public function getPlugins() { return $this->plugins; }
}
