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
 * @Entity
 * @Table(name="template_map_item",  uniqueConstraints={@UniqueConstraint(name="plugin_template_unique",columns={"pluginConfig_id", "templateMap_id"})})
 */
class TemplateMapItem extends BaseEntity
{

    public function __construct($templateMap = null, $pluginConfig=null, $container=null)
    {
        $this->templateMap = $templateMap;
        $this->pluginConfig = $pluginConfig;
        $this->container = $container;
    }

	/**
	 * @var TemplateMap
	 * @ManyToOne(targetEntity="TemplateMap")
	 * @JoinColumn(nullable=false, onDelete="cascade")
	 */
	private $templateMap;
	
	/**
	 * @var PluginConfig
	 * @ManyToOne(targetEntity="PluginConfig")
	 * @JoinColumn(nullable=false, onDelete="cascade")
	 */
	private $pluginConfig;
	
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
	public function getPluginConfig()
	{
	    return $this->pluginConfig;
	}
	
	/**
	 * @return TemplateMap
	 */
	public function getTemplateMap()
	{
	    return $this->templateMap;
	}
	
	/**
	 * @return integer
	 */
	public function getSort()
	{
	    return $this->sort;
	}
	
}
