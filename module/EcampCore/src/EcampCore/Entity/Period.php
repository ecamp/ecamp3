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

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation as Form;

use EcampLib\Entity\BaseEntity;

/**
 * A period is defined by its starting date and duration (in days).
 * A camp can consist of multiple, separated periods, which are not allowed to
 * overlap in time. However, a period can have multiple program alternatives (subcamps).
 * @ORM\Entity(repositoryClass="EcampCore\Repository\PeriodRepository")
 * @ORM\Table(name="periods")
 * @ORM\HasLifecycleCallbacks
 *
 * @Form\Name("period")
 */
class Period
    extends BaseEntity
{
    public function __construct(Camp $camp)
    {
        parent::__construct();

        $this->camp = $camp;
        $this->story = new Story();
        $this->days = new \Doctrine\Common\Collections\ArrayCollection();
        $this->eventInstances = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @ORM\Column(type="date", nullable=false )
     */
    protected $start;

    /**
     * @var Camp
     * @ORM\ManyToOne(targetEntity="Camp")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     * @Form\Exclude
     */
    private $camp;

    /**
     * @ORM\Column(type="text", nullable=true )
     */
    private $description;

    /**
     * @var Story
     * @ORM\OneToOne(targetEntity="Story", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="story_id", referencedColumnName="id")
     */
    private $story;

    /**
     * @ORM\OneToMany(targetEntity="Day", mappedBy="period", orphanRemoval=true)
     * @ORM\OrderBy({"dayOffset" = "ASC"})
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $days;

    /**
     * @ORM\OneToMany(targetEntity="EventInstance", mappedBy="period", orphanRemoval=true)
     * @ORM\OrderBy({"minOffsetStart" = "ASC", "createdAt" = "ASC"})
     * @var \Doctrine\Common\Collections\ArrayCollection
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
     * @return \EcampCore\Entity\Story
     */
    public function getStory()
    {
        return $this->story;
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
        return $this->start ? clone $this->start : null;
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
        $start = $this->start ? clone $this->start : null;

        if( $start )

            return $start->add($this->getDuration())
                     ->sub(new \DateInterval('PT1S'));
        else
            return null;
    }

    public function getRange()
    {
        $start = $this->getStart();
        $end = $this->getEnd();

        if ($start->format("Y") == $end->format("Y")) {
            if ($start->format("m") == $end->format("m")) {
                return $start->format("d.") . ' - ' . $end->format('d.m.Y');
            } else {
                return $start->format("d.m.") . ' - ' . $end->format('d.m.Y');
            }
        } else {
            return $start->format("d.m.Y") . ' - ' . $end->format('d.m.Y');
        }
    }

    /**
     * @return Camp
     */
    public function getCamp()
    {
        return $this->camp;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getDays()
    {
        return $this->days;
    }

    /**
     * @param $dayOffset
     * @return \EcampCore\Entity\Day
     */
    public function getDay($dayOffset)
    {
        $criteria = Criteria::create();
        $expr = Criteria::expr();
        $criteria->where($expr->eq('dayOffset', $dayOffset));

        return $this->days->matching($criteria)->first();
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getEventInstances()
    {
        return $this->eventInstances;
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        parent::PrePersist();

        $this->camp->addToList('periods', $this);
    }

    /**
     * @ORM\PreRemove
     */
    public function preRemove()
    {
        $this->camp->removeFromList('periods', $this);
    }
}
