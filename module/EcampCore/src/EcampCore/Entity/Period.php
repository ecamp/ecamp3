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

/**
 * A period is defined by its starting date and duration (in days).
 * A camp can consist of multiple, separated periods, which are not allowed to
 * overlap in time. However, a period can have multiple program alternatives (subcamps).
 * @ORM\Entity(repositoryClass="EcampCore\Repository\PeriodRepository")
 * @ORM\Table(name="periods")
 * @ORM\HasLifecycleCallbacks
 */
class Period
    extends BaseEntity
{
    public function __construct(Camp $camp)
    {
        parent::__construct();

        $this->camp = $camp;
        $this->days = new \Doctrine\Common\Collections\ArrayCollection();
        $this->eventInstances = new \Doctrine\Common\Collections\ArrayCollection();

        $this->camp->addToList('periods', $this);
    }

    /**
     * @ORM\Column(type="date", nullable=false )
     */
    private $start;

    /**
     * @var Camp
     * @ORM\ManyToOne(targetEntity="Camp")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    private $camp;

    /**
     * @ORM\Column(type="text", nullable=true )
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity="Day", mappedBy="period")
     * @ORM\OrderBy({"dayOffset" = "ASC"})
     * @var Doctrine\Common\Collections\ArrayCollection
     */
    private $days;

    /**
     * @ORM\OneToMany(targetEntity="EventInstance", mappedBy="period")
     */
    private $eventInstances;

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param \DateTime $start
     */
    public function setStart(\DateTime $start)
    {
        $start->setTime(0, 0);
        $this->start = $start;
    }

    /**
     * @return \DateTime
     */
    public function getStart()
    {
        $start = clone $this->start;

        return $start;
    }

    /**
     * @return \DateInterval
     */
    public function getDuration()
    {
        return new \DateInterval( 'P' . $this->getNumberOfDays() . 'D');
    }

    /**
     * @return int
     */
    public function getNumberOfDays()
    {
        return $this->days->count();
    }

    /**
     * @return \DateTime
     */
    public function getEnd()
    {
        $start = clone $this->start;

        return $start->add($this->getDuration())
                     ->sub(new \DateInterval('PT1S'));
    }

    /**
     * @return Camp
     */
    public function getCamp()
    {
        return $this->camp;
    }

    /**
     * @return Doctrine\Common\Collections\ArrayCollection
     */
    public function getDays()
    {
        return $this->days;
    }

    /**
     * @return Doctrine\Common\Collections\ArrayCollection
     */
    public function getEventInstances()
    {
        return $this->eventInstances;
    }

    /**
     * @ORM\PreRemove
     */
    public function preRemove()
    {
        $this->camp->removeFromList('periods', $this);
    }
}
