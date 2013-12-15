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

use EcampLib\Entity\BaseEntity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TemplateMapItem
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="plugin_positions", uniqueConstraints={@ORM\UniqueConstraint(name="plugin_template_unique",columns={"eventTypePlugin_id", "eventTemplate_id"})})
 */
class PluginPosition extends BaseEntity
{

    public function __construct(
        EventTemplate $eventTemplate = null,
        EventTypePlugin $eventTypePlugin = null,
        $container = null
    ) {
        $this->eventTemplate = $eventTemplate;
        $this->eventTypePlugin = $eventTypePlugin;
        $this->container = $container;
    }

    /**
     * @var EventTemplate
     * @ORM\ManyToOne(targetEntity="EventTemplate")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    private $eventTemplate;

    /**
     * @var EventTypePlugin
     * @ORM\ManyToOne(targetEntity="EventTypePlugin")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    private $eventTypePlugin;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    private $container;

    /**
     * @var integer
     * @ORM\Column(type="integer", nullable=false)
     */
    private $sort;

    /**
     * @return string
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @return EventTypePlugin
     */
    public function getEventTypePlugin()
    {
        return $this->eventTypePlugin;
    }

    /**
     * @return TemplateMap
     */
    public function getEventTemplate()
    {
        return $this->eventTemplate;
    }

    /**
     * @return integer
     */
    public function getSort()
    {
        return $this->sort;
    }

}
