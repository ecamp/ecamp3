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
    public function __construct(Camp $camp, EventPrototype $eventPrototype)
    {
        $this->camp = $camp;
        $this->eventPrototype = $eventPrototype;

        $this->pluginInstances = new \Doctrine\Common\Collections\ArrayCollection();
        $this->eventInstances = new \Doctrine\Common\Collections\ArrayCollection();
        $this->eventResps = new \Doctrine\Common\Collections\ArrayCollection();

        foreach ($eventPrototype->getPluginPrototypes() as $pluginPrototype) {

            $numInst = $pluginPrototype->getDefaultInstances();
            for ($i = 0; $i < $numInst; $i++) {
                $pluginInstance = new PluginInstance($this, $pluginPrototype);
                $this->pluginInstances->add($pluginInstance);
            }
        }

        $this->camp->addToList('events', $this);
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
     * @ORM\OneToMany(targetEntity="EventInstance", mappedBy="event", cascade={"all"}, orphanRemoval=true)
     */
    protected $eventInstances;

    /**
     * @ORM\OneToMany(targetEntity="PluginInstance", mappedBy="event", cascade={"all"}, orphanRemoval=true)
     */
    protected $pluginInstances;

    /**
     * @ORM\OneToMany(targetEntity="EventResp", mappedBy="event", cascade={"all"}, orphanRemoval=true)
     */
    protected $eventResps;

    /**
     * @var EventPrototype
     * @ORM\ManyToOne(targetEntity="EventPrototype")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    private $eventPrototype;

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
     * @return EventPrototype
     */
    public function getEventPrototype()
    {
        return $this->eventPrototype;
    }

    /**
     * @return array
     */
    public function getEventInstances()
    {
        return $this->eventInstances;
    }

    /**
     * @return array
     */
    public function getPluginInstances()
    {
        return $this->pluginInstances;
    }

    public function getEventResps()
    {
        return $this->eventResps;
    }

    /**
     * @return array
     */
    public function getPluginsByPrototype(PluginPrototype $pluginPrototype)
    {
        $closure = function(PluginInstance $instance) use ($pluginPrototype) {
            return $instance->getPluginPrototype()->getId() == $pluginPrototype->getId();
        };

        return $this->pluginInstances->filter($closure);
    }

    /**
     * @return integer
     */
    public function countPluginsByPrototype(PluginPrototype $pluginPrototype)
    {
        $closure = function(PluginInstance $instance) use ($pluginPrototype) {
            return $instance->getPluginPrototype()->getId() == $pluginPrototype->getId();
        };

        return $this->pluginInstances->count($closure);
    }

    /**
     * @ORM\PreRemove
     */
    public function preRemove()
    {
        $this->camp->removeFromList('events', $this);
    }

}
