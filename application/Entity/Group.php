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
	 * Short identifier, unique inside parent group
	 * @var string
	 * @Column(type="string", length=32, nullable=false )
	 */
	private $name;
	
	/**
	 * @ManyToOne(targetEntity="Group", inversedBy="children")
	 */
	private $parent;
	
	/**
     * @OneToMany(targetEntity="Group", mappedBy="parent")
     */
	private $children;
	
	/** @Column(type="string", length=64, nullable=false ) */
	private $description;
	
	/**
	 * @OneToMany(targetEntity="UserGroup", mappedBy="group")
	 */
	private $userGroup;
	
	/**
	 * @var Camp
	 * @OneToMany(targetEntity="Camp", mappedBy="group")
	 */
	private $camps;
	
	
	public function getId(){	return $this->id;	}
	
	public function getName()   { return $this->name; }
	public function setName( $name ){ $this->name = $name; return $this; }
	
	public function getDescription()   { return $this->description; }
	public function setDescription( $description ){ $this->description = $description; return $this; }

	public function getParent()   { return $this->parent; }
	public function setParent( $parent ){ $this->parent = $parent; return $this; }
	
	public function getChildren()   { return $this->children; }

	public function getMembers()
    {
	    /* TODO: check role of usergroup */
	    $query = $this->em->getRepository("Entity\User")->createQueryBuilder("u")->innerJoin("u.userGroup","ug")->where("ug.group = ".$this->id)->getQuery();

	    return $query->getResult();
	}
}