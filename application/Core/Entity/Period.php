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
 * A period is defined by its starting date and duration (in days). 
 * A camp can consist of multiple, separated periods, which are not allowed to 
 * overlap in time. However, a period can have multiple program alternatives (subcamps).
 * @Entity
 * @Table(name="periods")
 */
class Period extends BaseEntity
{

	public function __construct($camp = null)
	{
		$this->camp = $camp;
	}

	/**
	 * @var int
	 * @Id @Column(type="integer")
	 * @GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * @Column(type="date", nullable=false )
	 */
	private $start;

	/**
	 * @Column(type="integer", nullable=false )
	 */
	private $duration;

	/**
	 * @var Camp
	 * @ManyToOne(targetEntity="Camp")
	 * @JoinColumn(nullable=false, onDelete="cascade")
	 */
	private $camp;

	/**
	 * @OneToMany(targetEntity="Subcamp", mappedBy="period")
	 */
	private $subcamps;

	
	public function getId(){ return $this->id; }

	public function setStart($start){ $this->start = $start; }
	public function getStart()      { return $this->start;   } 

	public function setDuration($duration){ $this->duration = $duration; }
	public function getDutation()         { return $this->duration;      }

	public function getEnd(){ return $this->start->add( new \DateInterval( 'P'.($this->duration - 1).'D') ); }

	public function setCamp(Camp $camp){ $this->camp = $camp; }
	public function getCamp()          { return $this->camp; }

	public function getSubcamps() { return $this->subcamps; }

}
