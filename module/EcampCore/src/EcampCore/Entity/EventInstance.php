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
use Doctrine\Common\Collections\Criteria;

/**
 * Specifies the exact time/duration/subcamp when an event happens
 * @ORM\Entity(repositoryClass="EcampCore\Repository\EventInstanceRepository")
 * @ORM\Table(name="event_instances")
 */
class EventInstance
    extends BaseEntity
{

    /**
     * @var \EcampCore\Entity\Event
     * @ORM\ManyToOne(targetEntity="Event")
     * @ORM\JoinColumn(nullable=false)
     */
    private $event;

    /**
     * Start-Offset in minutes from the subcamp's starting date (00:00)
     * @ORM\Column(type="integer", nullable=false)
     */
    private $minOffsetStart;

    /**
     * End-Offset in minutes from the subcamp's starting date (00:00)
     * @ORM\Column(type="integer", nullable=false)
     */
    private $minOffsetEnd;

    /**
     * @ORM\ManyToOne(targetEntity="Period")
     * @ORM\JoinColumn(nullable=false)
     */
    private $period;

    /**
     * @param Event $event
     */
    public function __construct(Event $event)
    {
        parent::__construct();

        $this->event = $event;

        $this->minOffsetStart = 0;
        $this->minOffsetEnd = 0;
    }

    /**
     * @return Event
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @return \EcampCore\Entity\EventCategory
     */
    public function getEventCategory()
    {
        return $this->event->getEventCategory();
    }

    public function getNumberingStyle()
    {
        return $this->getEventCategory()->getNumberingStyle();
    }

    /**
     * @param DateInterval|int $offset
     */
    public function setOffset($offset)
    {
        if ($offset instanceof \DateInterval) {
            $offset =
                $offset->format('%a') * 24 * 60 +
                $offset->format('%h') * 60 +
                $offset->format('%i');
        }

        if ($offset < 0) {
            throw new \Exception("EventInstance offset can not be negative");
        }

        $shift = $offset - $this->minOffsetStart;

        $this->minOffsetStart = $offset;
        $this->minOffsetEnd  += $shift;
    }

    /**
     * @return \DateInterval
     */
    public function getOffset()
    {
        return new \DateInterval( 'PT' . $this->minOffsetStart . 'M');
    }

    /**
     * @return int
     */
    public function getOffsetInMinutes()
    {
        return $this->minOffsetStart;
    }

    /**
     * @param DateInterval|int $duration
     */
    public function setDuration($duration)
    {
        if ($duration instanceof \DateInterval) {
            $duration =
                $duration->format('%a') * 24 * 60 +
                $duration->format('%h') * 60 +
                $duration->format('%i');
        }

        $this->minOffsetEnd = $this->minOffsetStart + $duration;
    }

    /**
     * @return \DateInterval
     */
    public function getDuration()
    {
        $min = $this->getDurationInMinutes();

        $hour = floor($min / 60);
        $min = $min - 60 * $hour;

        return new \DateInterval( 'PT' . $hour . 'H' . $min . 'M');
    }

    /**
     * @return int
     */
    public function getDurationInMinutes()
    {
        return $this->minOffsetEnd - $this->minOffsetStart;
    }

    /**
     * @return \DateTime
     */
    public function getStartTime()
    {
        $start = clone $this->period->getStart();
        $start->add($this->getOffset());

        return $start;
    }

    /**
     * @return \DateTime
     */
    public function getEndTime()
    {
        $end = clone $this->getStartTime();
        $end->add($this->getDuration());

        return $end;
    }

    public function getDateRange()
    {
        $start = $this->getStartTime();
        $end = $this->getEndTime();

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
     * @param Period $period
     */
    public function setPeriod(Period $period)
    {
        $this->period = $period;
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

    public function getEventNumber()
    {
        return $this->getDayNumber() . '.' . $this->getMinorNumber();
    }

    private function getDayNumber()
    {
        return 1 + floor($this->getOffsetInMinutes() / (24*60));
    }

    private function getMinorNumber()
    {
        $period = $this->getPeriod();

        $dayNum = floor($this->getOffsetInMinutes() / (24*60));
        $dayOffset = 24 * 60 * $dayNum;

        $criteria = Criteria::create();
        $expr = Criteria::expr();
        $criteria->where($expr->andX(
            $expr->gte('minOffsetStart', $dayOffset),
            $expr->orX(
                $expr->lt('minOffsetStart', $this->getOffsetInMinutes()),
                $expr->andX(
                    $expr->eq('minOffsetStart', $this->getOffsetInMinutes()),
                    $expr->lt('createdAt', $this->getCreatedAt())
                )
            )
        ));

        $eventInstances = $period->getEventInstances()->matching($criteria);
        $num = $eventInstances
            ->filter(function($ei){ return $ei->getNumberingStyle() == $this->getNumberingStyle(); })
            ->count();

        return $this->getEventCategory()->getStyledNumber(1 + $num);
    }

}
