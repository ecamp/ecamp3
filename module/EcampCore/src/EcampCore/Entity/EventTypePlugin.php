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
use Doctrine\Common\Collections\Criteria;

/**
 * EventTypePlugin
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="event_type_plugins")
 * @ORM\HasLifecycleCallbacks
 */
class EventTypePlugin extends BaseEntity
{

    public function __construct(EventType $eventType = null, Plugin $plugin = null)
    {
        parent::__construct();

        $this->eventType = $eventType;
        $this->plugin = $plugin;
    }

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=false)
     */
    private $minNumberPluginInstances;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=false)
     */
    private $maxNumberPluginInstances;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $config;

    /**
     * @var EventType
     * @ORM\ManyToOne(targetEntity="EventType")
     * @ORM\JoinColumn(nullable=false)
     */
    private $eventType;

    /**
     * @var Plugin
     * @ORM\ManyToOne(targetEntity="Plugin")
     * @ORM\JoinColumn(nullable=false)
     */
    private $plugin;

    public function getMinNumberPluginInstances()
    {
        return $this->minNumberPluginInstances;
    }

    public function getMaxNumberPluginInstances()
    {
        return $this->maxNumberPluginInstances;
    }

    public function getConfig()
    {
        return \Zend\Json\Json::decode($this->config);
    }

    /**
     * @return \EcampCore\Entity\EventType
     */
    public function getEventType()
    {
        return $this->eventType;
    }

    /**
     * @return \EcampCore\Entity\Plugin
     */
    public function getPlugin()
    {
        return $this->plugin;
    }

    public function getActualNumberPluginInstances(Event $event)
    {
        if ($this->getEventType() != $event->getEventCategory()->getEventType()) {
            return 0;
        }

        $criteria = Criteria::create();
        $expr = Criteria::expr();
        $criteria->where($expr->eq('plugin', $this->getPlugin()));

        return $event->getEventPlugins()->matching($criteria)->count();
    }

    public function canAddPlugin(Event $event)
    {
        if ($this->getEventType() != $event->getEventCategory()->getEventType()) {
            return false;
        }

        return $this->getActualNumberPluginInstances($event) < $this->getMaxNumberPluginInstances();
    }

    public function canRemovePlugin(Event $event)
    {
        if ($this->getEventType() != $event->getEventCategory()->getEventType()) {
            return false;
        }

        return $this->getActualNumberPluginInstances($event) > $this->getMinNumberPluginInstances();
    }

    /**
     * @ORM\PrePersist
     */
    public function PrePersist()
    {
        parent::PrePersist();
        $this->eventType->addToList('eventTypePlugins', $this);
    }

    /**
     * @ORM\PreRemove
     */
    public function preRemove()
    {
        $this->eventType->removeFromList('eventTypePlugins', $this);
    }
}
