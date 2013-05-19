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
 * CampType
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="camp_types")
 */
class CampType extends BaseEntity
{

	public function __construct(){
		$this->eventTypes = new \Doctrine\Common\Collections\ArrayCollection();
	}

	/**
	 * @ORM\Column(type="string", length=64, nullable=false)
	 */
	private $name;
	
	
	/**
	 * @ORM\Column(type="string", length=32, nullable=false)
	 */
	private $type;
	
	
	/**
	 * @ORM\OneToMany(targetEntity="EventType", mappedBy="campType")
	 */
	private $eventTypes;
	
	
	/**
	 * @param string $name
	 */
	public function setName($name){
		$this->name = $name;
	}
	
	/**
	 * @return string
	 */
	public function getName(){
		return $this->name;
	}
	
	/**
	 * @param string $type
	 */
	public function setType($type){
		$this->type = $type;
	}
	
	/**
	 * @return string
	 */
	public function getType(){
		return $this->type;
	}
	
	/**
	 * @return Doctrine\Common\Collections\ArrayCollection
	 */
	public function getEventTypes(){
		return $this->eventTypes;
	}
}
