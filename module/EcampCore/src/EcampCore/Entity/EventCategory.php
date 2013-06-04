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
use EcampCore\Acl\BelongsToParentResource;

/**
 * EventCategory
 * @ORM\Entity
 * @ORM\Table(name="event_categories")
 */
class EventCategory
    extends BaseEntity
    implements BelongsToParentResource
{

    public function __construct()
    {
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
     * @param Camp $camp
     */
    public function setCamp(Camp $camp)
    {
        $this->camp = $camp;
    }

    /**
     * @return Camp
     */
    public function getCamp()
    {
        return $this->camp;
    }

    public function getParentResource()
    {
        return $this->camp;
    }

    /**
     * @param EventType $eventType
     */
    public function setEventType(EventType $eventType)
    {
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
