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

use EcampLib\Entity\BaseEntity;

/**
 * EventType
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="event_types")
 */
class EventType extends BaseEntity
{

	public function __construct(){
		$this->eventPrototypes = new \Doctrine\Common\Collections\ArrayCollection();
	}

	/**
	 * @ORM\Column(type="string", length=64, nullable=false)
	 */
	private $name;
	
	/**
	 * @ORM\Column(type="string", length=8, nullable=false)
	 */
	private $defaultColor;
	
	/**
	 * @ORM\Column(type="string", length=1, nullable=false)
	 */
	private $defaultNumberingStyle;
	
	/**
	 * @ORM\ManyToOne(targetEntity="CampType")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $campType;
	
	/**
	 * @ORM\ManyToMany(targetEntity="EventPrototype")
	 * @ORM\JoinTable(name="event_type_event_prototypes",
	 *      joinColumns={@ORM\JoinColumn(name="eventtype_id", referencedColumnName="id")},
	 *      inverseJoinColumns={@ORM\JoinColumn(name="eventprototype_id", referencedColumnName="id")}
	 *      )
	 */
	private $eventPrototypes;
	
	
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
	 * @param string $color
	 */
	public function setDefaultColor($color){
		$this->defaultColor = $color;
	}
	
	/**
	 * @return string
	 */
	public function getDefaultColor(){
		return $this->defaultColor;
	}
	
	/**
	 * @param string $numberingStyle
	 * @throws OutOfRangeException
	 */
	public function setDefaultNumberingStyle($numberingStyle){
		$allowed = array('1', 'a', 'A', 'i', 'I');
		if(in_array($numberingStyle, $allowed)){
			$this->defaultNumberingStyle = $numberingStyle;
		} else {
			throw new OutOfRangeException("Unknown NumberingStyle");
		}
	}
	
	/**
	 * @return string
	 */
	public function getDefaultNumberingStyle(){
		return $this->defaultNumberingStyle;
	}
	
	
	/**
	 * @param CampType $campType
	 */
	public function setCampType(CampType $campType){
		$this->campType = $campType;
	}
	
	/**
	 * @return CampType
	 */
	public function getCampType(){
		return $this->campType;
	}
}
