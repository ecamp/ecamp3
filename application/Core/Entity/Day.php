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
 * The day belongs to the subcamp and can provide additional background
 * to a specific day (e.g. storyline, menu, responsible leader of the day,
 * etc.). The events however are not connected with the days in particular.
 * @Entity
 * @Table(name="days", uniqueConstraints={@UniqueConstraint(name="offset_subcamp_idx", columns={"dayOffset", "subcamp_id"})})
 */
class Day extends BaseEntity
{

	/**
	 * @return \CoreApi\Entity\Day
	 */
	public function asReadonly()
	{
		return new \CoreApi\Entity\Day($this);
	}


	/**
	 * @var int
	 * @Id @Column(type="integer")
	 * @GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * Offset to the start date of the subcamp's period
	 * @Column(type="integer")
	 */
	private $dayOffset;

	/**
	 * @var Subcamp
	 * @ManyToOne(targetEntity="Subcamp")
	 * @JoinColumn(nullable=false)
	 */
	private $subcamp;

	/**
	 * @Column(type="text")
	 */
	private $notes;

	/** @Public:Method() */
	public function getId()
	{
		return $this->id;
	}

	public function setDayOffset($offset)
	{
		$this->dayOffset = $offset;
	}
	
	/** @Public:Method() */
	public function getDayOffset()
	{
		return $this->dayOffset;
	}

	public function setNotes($notes)
	{
		$this->notes = $notes;
	}
	
	/** @Public:Method() */
	public function getNotes()
	{
		return $this->notes;
	}

	public function setSubcamp(subcamp $subcamp)
	{
		$this->subcamp = $subcamp;
	}
	
	/**
	 * @Public:MethodEntity()
	 * @return \Core\Entity\Subcamp
	 */
	public function getSubcamp()
	{
		return $this->subcamp;
	}

}
