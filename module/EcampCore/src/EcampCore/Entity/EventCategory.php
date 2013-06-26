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
 * @ORM\Entity
 * @ORM\Table(name="event_categories")
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
    	if($this->getCamp()->getCampType() !== $eventType->getCampType()){
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
}
