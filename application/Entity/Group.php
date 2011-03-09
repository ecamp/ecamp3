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

namespace Entity;

/**
 * @Entity
 * @Table(name="groups")
 */
class Group extends BaseEntity
{
	public function __construct()
    {
		$this->children = new \Doctrine\Common\Collections\ArrayCollection();
		$this->userGroup = new \Doctrine\Common\Collections\ArrayCollection();
    }
	
	/**
	 * @Id @Column(type="integer")
	 * @GeneratedValue(strategy="AUTO")
	 * @var int
	 */
	private $id;
	
	/**
	 * @ManyToOne(targetEntity="Group", inversedBy="children")
	 */
	private $parent;
	
	/**
     * @OneToMany(targetEntity="Group", mappedBy="parent")
     */
	private $children;
	
	/**
	 * @OneToOne(targetEntity="Name", cascade={"all"}, fetch="EAGER")
	 */
	private $name;
	
	/** @Column(type="string", length=64, nullable=false ) */
	private $description;
	
	/**
	 * @OneToMany(targetEntity="UserGroup", mappedBy="group")
	 */
	private $userGroup;
	
	
	public function getId(){	return $this->id;	}
	
	public function getDescription()   { return $this->description; }
	public function setDescription( $description ){ $this->description = $description; return $this; }

	public function getParent()   { return $this->parent; }
	public function setParent( $parent ){ $this->parent = $parent; return $this; }
	
	public function getChildren()   { return $this->children; }
	
	public function getName()
	{
		return $this->name->getName();
	}
	
	public function setName($name)
	{
		if( !isset($this->name) )
		{
			$this->name = new Name();
		}
		
		$this->name->setName($name);
		return $this;
	}
}