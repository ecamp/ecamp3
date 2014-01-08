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
 * The day belongs to the subcamp and can provide additional background
 * to a specific day (e.g. storyline, menu, responsible leader of the day,
 * etc.). The events however are not connected with the days in particular.
 * @ORM\Entity(repositoryClass="EcampCore\Repository\DayRepository")
 * @ORM\Table(name="days", uniqueConstraints={@ORM\UniqueConstraint(name="offset_period_idx", columns={"dayOffset", "period_id"})})
 * @ORM\HasLifecycleCallbacks
 */
class Day
    extends BaseEntity
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
     * @var Story
     * @ORM\OneToOne(targetEntity="Story", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="story_id", referencedColumnName="id")
     */
    private $story;


    public function __construct(Period $period, $dayOffset)
    {
        parent::__construct();

        $this->period = $period;
        $this->dayOffset = $dayOffset;
        $this->story = new Story();
    }



    /**
     * @return int
     */
    public function getDayOffset()
    {
        return $this->dayOffset;
    }


    /**
     * @return string
     */
    public function getStory()
    {
        return $this->story;
    }


    /**
     * @return \DateTime
     */
    public function getStart()
    {
        $start = clone $this->period->getStart();
        $start->add(new \DateInterval( 'P' . $this->dayOffset . 'D'));

        return $start;
    }


    /**
     * @return \DateTime
     */
    public function getEnd()
    {
        $end = clone $this->period->getStart();
        $end->add(new \DateInterval( 'P' . ($this->dayOffset + 1) . 'D'))
            ->sub(new \DateInterval('PT1S'));

        return $end;
    }

    /**
     * @return Period
     */
    public function getPeriod()
    {
        return $this->period;
    }

    /**
     * @return Camp
     */
    public function getCamp()
    {
        return $this->period->getCamp();
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        parent::PrePersist();

        $this->period->addToList('days', $this);
    }

    /**
     * @ORM\PreRemove
     */
    public function preRemove()
    {
        $this->period->removeFromList('days', $this);
    }

}
