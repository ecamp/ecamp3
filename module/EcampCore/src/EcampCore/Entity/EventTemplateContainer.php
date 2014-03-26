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
 * @ORM\Entity(readOnly=true, repositoryClass="EcampCore\Repository\EventTemplateContainerRepository")
 * @ORM\Table(name="event_template_containers", uniqueConstraints={
 * 	@ORM\UniqueConstraint(
 * 		name="eventTemplate_containerName_unique",
 * 		columns={"eventTemplate_id", "containerName"}
 * 	)
 * })
 */
class EventTemplateContainer extends BaseEntity
{

    public function __construct(
        EventTemplate $eventTemplate,
        EventTypePlugin $eventTypePlugin,
        $containerName
    ) {
        parent::__construct();

        $this->eventTemplate = $eventTemplate;
        $this->eventTypePlugin = $eventTypePlugin;
        $this->containerName = $containerName;
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
     * @ORM\Column(type="string", length=128, nullable=false )
     */
    private $containerName;

    /**
     * @var string
     * @ORM\Column(type="string", length=128, nullable=false )
     */
    private $filename;

    /**
     * @return EventTemplate
     */
    public function getEventTemplate()
    {
        return $this->eventTemplate;
    }

    /**
     * @return EventTypePlugin
     */
    public function getEventTypePlugin()
    {
        return $this->eventTypePlugin;
    }

    /**
     * @return string
     */
    public function getContainerName()
    {
        return $this->containerName;
    }

    /**
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

}
