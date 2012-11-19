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

namespace CoreApi\Entity;

/**
 * TemplateMapItem
 * @Entity(readOnly=true)
 * @Table(name="plugin_positions", uniqueConstraints={@UniqueConstraint(name="plugin_template_unique",columns={"pluginPrototype_id", "eventTemplate_id"})})
 */
class PluginPosition extends BaseEntity
{

    public function __construct($eventTemplate = null, $pluginPrototype = null, $container = null)
    {
        $this->eventTemplate = $eventTemplate;
        $this->pluginPrototype = $pluginPrototype;
        $this->container = $container;
    }

	/**
	 * @var EventTemplate
	 * @ManyToOne(targetEntity="EventTemplate")
	 * @JoinColumn(nullable=false, onDelete="cascade")
	 */
	private $eventTemplate;
	
	/**
	 * @var PluginPrototype
	 * @ManyToOne(targetEntity="PluginPrototype")
	 * @JoinColumn(nullable=false, onDelete="cascade")
	 */
	private $pluginPrototype;
	
	/**
	 * @var string
	 * @Column(type="string", nullable=false)
	 */
	private $container;
	
	/**
	 * @var integer
	 * @Column(type="integer", nullable=false)
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
	 * @return PluginConfig
	 */
	public function getPluginPrototype()
	{
	    return $this->pluginPrototype;
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
