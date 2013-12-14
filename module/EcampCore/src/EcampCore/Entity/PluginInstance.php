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
 * @ORM\Entity(repositoryClass="EcampCore\Repository\PluginInstanceRepository")
 * @ORM\Table(name="plugin_instances")
 */
class PluginInstance
    extends BaseEntity
{

    public function __construct(Event $event, PluginPrototype $pluginPrototype)
    {
        parent::__construct();

        $this->event = $event;
        $this->pluginPrototype = $pluginPrototype;
    }

    /**
     * @ORM\ManyToOne(targetEntity="Event")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    public $event;

    /**
     * @var PluginPrototype
     * @ORM\ManyToOne(targetEntity="PluginPrototype")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    protected $pluginPrototype;

    /**
     * This var contains an instance of $this->pluginStrategy.
     * The instance is loaded with a PostLoad event listener and will not be persisted by Doctrine.
     *
     * @var IPluginStrategy
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
     * Returns the plugin prototype
     *
     * @return PluginPrototype
     */
    public function getPluginPrototype()
    {
        return $this->pluginPrototype;
    }

    /**
     * Returns the plugin name
     *
     * @return string
     */
    public function getPluginName()
    {
        return $this->getPluginPrototype()->getPlugin()->getName();
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
        return $this->getPluginPrototype()->getPlugin()->getStrategyClass();
    }

    /**
     * Returns the instantiated strategy
     *
     * @return EcampCore\Plugin\AbstractStrategy
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

        $this->event->addToList('pluginInstances', $this);
    }

    /**
     * @ORM\PreRemove
     */
    public function preRemove()
    {
        $this->event->removeFromList('pluginInstances', $this);
    }
}
