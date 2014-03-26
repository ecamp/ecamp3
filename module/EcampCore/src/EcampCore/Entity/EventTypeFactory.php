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

/**
 * EventTypeFactory
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="event_type_factories")
 * @ORM\HasLifecycleCallbacks
 */
class EventTypeFactory extends BaseEntity
{

    public function __construct(EventType $eventType)
    {
        parent::__construct();

        $this->eventType = $eventType;
    }

    /**
     * @ORM\Column(type="string", length=64, nullable=false)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=128, nullable=false)
     */
    private $factoryName;

    /**
     * @var EventType
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

    public function setFactoryName($factoryName)
    {
        $this->factoryName = $factoryName;
    }

    /**
     * @return string
     */
    public function getFactoryName()
    {
        return $this->factoryName;
    }
}
