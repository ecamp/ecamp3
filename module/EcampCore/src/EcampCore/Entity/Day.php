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

namespace EcampCore\Entity;

use Doctrine\ORM\Mapping as ORM;

use EcampLib\Entity\BaseEntity;
use EcampCore\Acl\BelongsToParentResource;

/**
 * The day belongs to the subcamp and can provide additional background
 * to a specific day (e.g. storyline, menu, responsible leader of the day,
 * etc.). The events however are not connected with the days in particular.
 * @ORM\Entity(repositoryClass="EcampCore\Repository\DayRepository")
 * @ORM\Table(name="days", uniqueConstraints={@ORM\UniqueConstraint(name="offset_period_idx", columns={"dayOffset", "period_id"})})
 */
class Day 
	extends BaseEntity
	implements BelongsToParentResource
{

	/**
	 * Offset to the start date of the subcamp's period
	 * @ORM\Column(type="integer")
	 */
	private $dayOffset;

	/**
	 * @var Period
	 * @ORM\ManyToOne(targetEntity="Period")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $period;

	/**
	 * @ORM\Column(type="text", nullable=true)
	 */
	private $notes;
	

	public function __construct(Period $period, $dayOffset)
	{
		parent::__construct();
		
		$this->period = $period;
		$this->dayOffset = $dayOffset;
	}
	
	

	/**
	 * @return int
	 */
	public function getDayOffset()
	{
		return $this->dayOffset;
	}

	
	/**
	 * @param stirng $notes
	 */
	public function setNotes($notes)
	{
		$this->notes = $notes;
	}
	
	
	/**
	 * @return string
	 */
	public function getNotes()
	{
		return $this->notes;
	}
	
	
	/**
	 * @return \DateTime
	 */
	public function getStart()
	{
		$start = $this->period->getStart()->add(new \DateInterval( 'P' . $this->dayOffset . 'D'));
		return $start;
	}
	
	
	/**
	 * @return \DateTime
	 */
	public function getEnd()
	{
		$end = $this->getStart()->add(new \DateInterval( 'P' . ($this->dayOffset + 1) . 'D'));
		return $end;
	}
	
	
	/**
	 * @return Period
	 */
	public function getPeriod()
	{
		return $this->period;
	}
	
	
	public function getParentResource(){
		return $this->period;
	}

	
	/**
	 * @return Camp
	 */
	public function getCamp()
	{
		return $this->period->getCamp();
	}
	
}
