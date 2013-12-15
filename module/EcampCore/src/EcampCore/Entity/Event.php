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
 * Container for an event.
 * - An event has no date/time, as it only describes the program but not when it happens.
 * - An event can either belong to a camp or to a user
 * @ORM\Entity(repositoryClass="EcampCore\Repository\EventRepository")
 * @ORM\Table(name="events")
 * @ORM\HasLifecycleCallbacks
 */
class Event
    extends BaseEntity
{
    public function __construct(Camp $camp, EventCategory $eventCategory)
    {
        $this->camp = $camp;
        $this->eventCategory = $eventCategory;

        $this->eventPlugins = new \Doctrine\Common\Collections\ArrayCollection();
        $this->eventInstances = new \Doctrine\Common\Collections\ArrayCollection();
        $this->eventResps = new \Doctrine\Common\Collections\ArrayCollection();

//         $eventType = $eventCategory->getEventType();
//         foreach ($eventType->getEventTypePlugins() as $eventTypePlugin) {
//         }
//         foreach ($eventPrototype->getPluginPrototypes() as $pluginPrototype) {
//             $numInst = $pluginPrototype->getDefaultInstances();
//             for ($i = 0; $i < $numInst; $i++) {
//                 $pluginInstance = new PluginInstance($this, $pluginPrototype);
//                 $this->pluginInstances->add($pluginInstance);
//             }
//         }
    }

    /**
     * @ORM\Column(type="text")
     */
    private $title;

    /**
     * @ORM\ManyToOne(targetEntity="Camp")
     */
    private $camp;

    /**
     * @var EventCategory
     * @ORM\ManyToOne(targetEntity="EventCategory")
     * @ORM\JoinColumn(nullable=false)
     */
    private $eventCategory;

    /**
     * @ORM\OneToMany(targetEntity="EventInstance", mappedBy="event", cascade={"all"}, orphanRemoval=true)
     */
    protected $eventInstances;

    /**
     * @ORM\OneToMany(targetEntity="EventPlugin", mappedBy="event", cascade={"all"}, orphanRemoval=true)
     */
    protected $eventPlugins;

    /**
     * @ORM\OneToMany(targetEntity="EventResp", mappedBy="event", cascade={"all"}, orphanRemoval=true)
     */
    protected $eventResps;

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return Camp
     */
    public function getCamp()
    {
        return $this->camp;
    }

    /**
     * @param EventCategory $eventCategory
     */
    public function setEventCategory(EventCategory $eventCategory)
    {
        $this->eventCategory = $eventCategory;
    }

    /**
     * @return EventCategory
     */
    public function getEventCategory()
    {
        return $this->eventCategory;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getEventInstances()
    {
        return $this->eventInstances;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getEventPlugins()
    {
        return $this->eventPlugins;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getEventResps()
    {
        return $this->eventResps;
    }

    /**
     * @return array
     */
    public function getEventPluginsByPlugin(Plugin $plugin)
    {
        $closure = function(EventPlugin $eventPlugin) use ($plugin) {
            return $eventPlugin->getPlugin()->getId() == $plugin->getId();
        };

        return $this->eventPlugins->filter($closure);
    }

    /**
     * @return integer
     */
    public function countEventPluginsByPlugin(Plugin $plugin)
    {
        $closure = function(EventPlugin $eventPlugin) use ($plugin) {
            return $eventPlugin->getPlugin()->getId() == $plugin->getId();
        };

        return $this->eventPlugins->count($closure);
    }

    /**
     * @ORM\PrePersist
     */
    public function PrePersist()
    {
        parent::PrePersist();

        $this->camp->addToList('events', $this);
    }

    /**
     * @ORM\PreRemove
     */
    public function preRemove()
    {
        $this->camp->removeFromList('events', $this);
    }

}
