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
 * A subcamp is a container for program. Multiple subcamps can happen at the same time,
 * if they are assigned to the same peridos.
 * @Entity
 * @Table(name="subcamps")
 */
class Subcamp extends BaseEntity
{

	/**
	 * @return \CoreApi\Entity\Subcamp
	 */
	public function asReadonly()
	{
		return new \CoreApi\Entity\Subcamp($this);
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
	private $description;

	/**
	 * @var Period
	 * @ManyToOne(targetEntity="Period")
	 * @JoinColumn(nullable=false)
	 */
	private $period;

	/**
	 * @OneToMany(targetEntity="Day", mappedBy="subcamp")
	 * @OrderBy({"offset" = "ASC"})
	 */
	private $days;

	/**
	 * @OneToMany(targetEntity="EventInstance", mappedBy="subcamp")
	 */
	private $eventInstances;


	/**
	 * @Public:Method()
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	public function setDescription($description)
	{
		$this->description = $description;
	}
	
	/**
	 * @Public:Method()
	 * @return string
	 */
	public function getDescription()            
	{
		return $this->description;
	}

	public function setPeriod(Period $period)
	{
		$this->period = $period;
	}
	
	/**
	 * @Public:MethodEntity()
	 * @return Period
	 */
	public function getPeriod()               
	{
		return $this->period;
	}

	/**
	 * @Public:MethodEntityList(type = "\CoreApi\Entity\EventInstance")
	 * @return array
	 */
	public function getEventInstances() 
	{
		return $this->eventInstances;
	}
}
