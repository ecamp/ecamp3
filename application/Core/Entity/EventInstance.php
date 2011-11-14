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
 * Specifies the exact time/duration/subcamp when an event happens
 * @Entity
 * @Table(name="event_instances")
 */
class EventInstance extends BaseEntity
{

	/**
	 * @var int
	 * @Id @Column(type="integer")
	 * @GeneratedValue(strategy="AUTO")
	 */
	private $id;
	
	/**
	 * @ManyToOne(targetEntity="Event")
	 * @JoinColumn(nullable=false)
	 */
	private $event;

	/**
	 * Offset in minutes from the subcamp's starting date (00:00)
	 * @Column(type="integer" )
	 */
	private $minOffset;
	
	/**
	 * Duration of this instance in minutes
	 * @Column(type="integer" )
	 */
	private $duration;
	
	/**
	 * @ManyToOne(targetEntity="Subcamp")
	 * @JoinColumn(nullable=false)
	 */
	private $subcamp;

	
	public function getId(){ return $this->id; }

	public function setEvent(Event $event){ $this->event = $event; }
	public function getEvent()            { return $this->event;   }
	
	public function setMinOffset($minOffset){ $this->minOffset = $minOffset; }
	public function getMinOffset()          { return $this->minOffset; }
	
	public function setDuration($duration){ $this->duration = $duration; }
	public function getDuration()         { return $this->duration; }
	
	public function setSubcamp(Subcamp $subcamp){ $this->subcamp = $subcamp; }
	public function getSubcamp()                { return $this->subcamp; }
	
}
