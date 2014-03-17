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

use OutOfRangeException;
use EcampLib\Entity\BaseEntity;

/**
 * EventCategory
 * @ORM\Entity(repositoryClass="EcampCore\Repository\EventCategoryRepository")
 * @ORM\Table(name="event_categories")
 * @ORM\HasLifecycleCallbacks
 */
class EventCategory
    extends BaseEntity
{

    public function __construct(Camp $camp, EventType $eventType)
    {
        $this->camp = $camp;
        $this->setEventType($eventType);

        $this->setColor($eventType->getDefaultColor());
        $this->setNumberingStyle($eventType->getDefaultNumberingStyle());
    }

    /**
     * @ORM\Column(type="string", length=64, nullable=false)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=16, nullable=false)
     */
    private $short;

    /**
     * @ORM\Column(type="string", length=8, nullable=false)
     */
    private $color;

    /**
     * @ORM\Column(type="string", length=1, nullable=false)
     */
    private $numberingStyle;

    /**
     * @ORM\ManyToOne(targetEntity="Camp")
     * @ORM\JoinColumn(nullable=false)
     */
    private $camp;

    /**
     * @ORM\ManyToOne(targetEntity="EventType")
     * @ORM\JoinColumn(nullable=false)
     */
    private $eventType;

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $short
     */
    public function setShort($short)
    {
        $this->short = $short;
    }

    /**
     * @return string
     */
    public function getShort()
    {
        return $this->short;
    }

    /**
     * @param string $color
     */
    public function setColor($color)
    {
        $this->color = $color;
    }

    /**
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @param  string              $numberingStyle
     * @throws OutOfRangeException
     */
    public function setNumberingStyle($numberingStyle)
    {
        $allowed = array('1', 'a', 'A', 'i', 'I');
        if (in_array($numberingStyle, $allowed)) {
            $this->numberingStyle = $numberingStyle;
        } else {
            throw new OutOfRangeException("Unknown NumberingStyle");
        }
    }

    /**
     * @return string
     */
    public function getNumberingStyle()
    {
        return $this->numberingStyle;
    }

    /**
     * @return Camp
     */
    public function getCamp()
    {
        return $this->camp;
    }

    /**
     * @param EventType $eventType
     */
    public function setEventType(EventType $eventType)
    {
        if ($this->getCamp()->getCampType() !== $eventType->getCampType()) {
            throw new \Exception(sprintf(
                "EventType '%s' is not availlable for CampType '%s'",
                $eventType->getName(),
                $this->getCamp()->getCampType()->getName()
            ));
        }

        $this->eventType = $eventType;
    }

    /**
     * @return EventType
     */
    public function getEventType()
    {
        return $this->eventType;
    }

    public function getStyledNumber($num)
    {
        switch ($this->numberingStyle) {
            case '1':
                return $num;
            case 'a':
                return strtolower($this->getAlphaNum($num));
            case 'A':
                return strtoupper($this->getAlphaNum($num));
            case 'i':
                return strtolower($this->getRomanNum($num));
            case 'I':
                return strtoupper($this->getRomanNum($num));
            default:
                return $num;
        }
    }

    private function getAlphaNum($num)
    {
        $num--;
        $alphaNum = '';
        if ($num >= 26) {
            $alphaNum .= $this->getAlphaNum(floor($num / 26));
        }
        $alphaNum .= chr(97 + ($num % 26));

        return $alphaNum;
    }

    private function getRomanNum($num)
    {
        $table = array('M'=>1000, 'CM'=>900, 'D'=>500, 'CD'=>400, 'C'=>100, 'XC'=>90, 'L'=>50, 'XL'=>40, 'X'=>10, 'IX'=>9, 'V'=>5, 'IV'=>4, 'I'=>1);
        $romanNum = '';
        while ($num > 0) {
            foreach ($table as $rom => $arb) {
                if ($num >= $arb) {
                    $num -= $arb;
                    $romanNum .= $rom;
                    break;
                }
            }
        }

        return $romanNum;
    }

    /**
     * @ORM\PrePersist
     */
    public function PrePersist()
    {
        parent::PrePersist();

        $this->camp->addToList('eventCategories', $this);
    }

    /**
     * @ORM\PreRemove
     */
    public function preRemove()
    {
        $this->camp->removeFromList('eventCategories', $this);
    }
}
