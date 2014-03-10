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

use EcampLib\Entity\BaseEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="EcampCore\Repository\EventPluginRepository")
 * @ORM\Table(name="event_plugins")
 */
class EventPlugin
    extends BaseEntity
{

    public function __construct(
        Event $event = null,
        Plugin $plugin = null,
        $instanceName = null
    ) {
        parent::__construct();

        $this->event = $event;
        $this->plugin = $plugin;
    }

    /**
     * @var Event
     * @ORM\ManyToOne(targetEntity="Event")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    public $event;

    /**
     * @var Plugin
     * @ORM\ManyToOne(targetEntity="Plugin")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $plugin;

    /**
     * @ORM\Column(type="string", length=64, nullable=false)
     */
    private $instanceName;

    /**
     * This var contains an instance of $this->pluginStrategy.
     * The instance is loaded with a PostLoad event listener and will not be persisted by Doctrine.
     *
     * @var \EcampCore\Plugin\AbstractStrategy
     */
    protected $strategyInstance;

    public function getEvent()
    {
        return $this->event;
    }

    public function getCamp()
    {
        return $this->event->getCamp();
    }

    /**
     * Returns the plugin
     *
     * @return Plugin
     */
    public function getPlugin()
    {
        return $this->plugin;
    }

    /**
     * Returns the plugin name
     *
     * @return string
     */
    public function getPluginName()
    {
        return $this->getPlugin()->getName();
    }

    public function getInstanceName()
    {
        return $this->instanceName;
    }

    /**
     * Returns the strategy that is used for this pluginitem.
     *
     * The strategy itself defines how this plugin can be rendered etc.
     *
     * @return string
     */
    public function getPluginStrategyClass()
    {
        return $this->getPlugin()->getStrategyClass();
    }

    /**
     * Returns the instantiated strategy
     *
     * @return \EcampCore\Plugin\AbstractStrategy
     */
    public function getStrategyInstance()
    {
        if ($this->strategyInstance == null) {
            $classname = $this->getPluginStrategyClass();
            $this->strategyInstance = new $classname($this);
        }

        return $this->strategyInstance;
    }

    /**
     * @ORM\PrePersist
     */
    public function PrePersist()
    {
        parent::PrePersist();

        $this->event->addToList('eventPlugins', $this);
    }

    /**
     * @ORM\PreRemove
     */
    public function preRemove()
    {
        $this->event->removeFromList('eventPlugins', $this);
    }
}
