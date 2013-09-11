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
 * EventTemplate
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="event_templates", uniqueConstraints={@ORM\UniqueConstraint(name="prototype_medium_unique",columns={"eventPrototype_id", "medium"})})
 */
class EventTemplate extends BaseEntity
{

    public function __construct(EventPrototype $eventPrototype, Medium $medium, $filename)
    {
        $this->medium = $medium;
        $this->eventPrototype = $eventPrototype;
        $this->filename = $filename;

        $this->pluginPositions = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @var Medium
     * @ORM\ManyToOne(targetEntity="Medium")
     * @ORM\JoinColumn(name="medium", referencedColumnName="name", nullable=false, onDelete="cascade")
     */
    private $medium;

    /**
     * @var string
     * @ORM\Column(type="string", length=128, nullable=false )
     */
    private $filename;

    /**
     * @var EventPrototype
     * @ORM\ManyToOne(targetEntity="EventPrototype")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    private $eventPrototype;

    /**
     * @ORM\OneToMany(targetEntity="PluginPosition", mappedBy="eventTemplate")
     * @ORM\OrderBy({"sort" = "ASC"})
     */
    private $pluginPositions;

    /**
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @return Medium
     */
    public function getMedium()
    {
        return $this->medium;
    }

    /**
     * @return EventPrototype
     */
    public function getEventPrototype()
    {
        return $this->eventPrototype;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getPluginPositions()
    {
        return $this->pluginPositions;
    }
}
