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
 * EventTemplate
 * @Entity(readOnly=true)
 * @Table(name="event_templates", uniqueConstraints={@UniqueConstraint(name="prototype_medium_unique",columns={"eventPrototype_id", "medium"})})
 */
class EventTemplate extends BaseEntity
{

    public function __construct($eventPrototype = null)
    {
        $this->eventPrototype = $eventPrototype;
    }

    /**
     * @var Medium
     * @ManyToOne(targetEntity="Medium")
     * @JoinColumn(name="medium", referencedColumnName="name", nullable=false, onDelete="cascade")
     */
    private $medium;
	
	/**
	 * @var string
	 * @Column(type="string", length=128, nullable=false )
	 */
	private $filename;
	
	/**
	 * @var EventPrototype
	 * @ManyToOne(targetEntity="EventPrototype")
	 * @JoinColumn(nullable=false, onDelete="cascade")
	 */
	private $eventPrototype;
	
	/**
	 * @OneToMany(targetEntity="PluginPosition", mappedBy="eventTemplate")
	 * @OrderBy({"sort" = "ASC"})
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
	 * @return string
	 */
	public function getMedium()
	{
	    return $this->medium;
	}
	
	public function getPluginPositions()
	{
	    return $this->pluginPositions;
	}
}
