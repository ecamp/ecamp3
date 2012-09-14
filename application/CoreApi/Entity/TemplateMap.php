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
 * TemplateMap
 * @Entity
 * @Table(name="template_map",  uniqueConstraints={@UniqueConstraint(name="prototype_medium_unique",columns={"prototype_id", "medium"})})
 */
class TemplateMap extends BaseEntity
{

    public function __construct($prototype = null)
    {
        $this->prototype = $prototype;
    }

	/**
	 * @var string
	 * @Column(type="string", length=64, nullable=false )
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
	private $prototype;
	
	/**
	 * @OneToMany(targetEntity="TemplateMapItem", mappedBy="templateMap")
	 * @OrderBy({"sort" = "ASC"})
	 */
	private $items;
	
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
	
	public function getItems()
	{
	    return $this->items;
	}
}
