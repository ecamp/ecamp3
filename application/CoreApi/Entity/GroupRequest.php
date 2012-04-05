<?php
/*
 * Copyright (C) 2012 Aurelian Ammon
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
 * @Entity
 * @Table(name="grouprequests")
 */
class GroupRequest extends BaseEntity
{

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
	 * @ManyToOne(targetEntity="Group")
	 */
	private $parent;

	/**
	 * @Column(type="string", length=64, nullable=false )
	 */
	private $description;

	/**
	 * @var CoreApi\Entity\Image
	 * @OneToOne(targetEntity="Image")
	 * @JoinColumn(name="image_id", referencedColumnName="id")
	 */
	private $image;

	/**
	 * @Column(type="text", nullable=false )
	 */
	private $motivation;

	/**
	 * @var Requester
	 * @ManyToOne(targetEntity="User")
	 */
	private $requester;
	
	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}
	
	
	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}
	
	public function setName( $name )
	{
		$this->name = $name; return $this;
	}

	
	/**
	 * @return string
	 */
	public function getDescription()
	{
		return $this->description;
	}
	
	public function setDescription( $description )
	{
		$this->description = $description; return $this;
	}

	/**
	 * @return string
	 */
	public function getMotivation()
	{
		return $this->motivation;
	}
	
	public function setMotivation( $motivation )
	{
		$this->motivation = $motivation; return $this;
	}

	/**
	 * @return User  
	 */
	public function getRequester()
	{
		return $this->requester;
	}
		
	public function setRequester(User $requester )
	{
		$this->requester = $requester; return $this;
	}

	
	/**
	 * @return Group
	 */
	public function getParent()
	{
		return $this->parent;
	}
	
	public function setParent(Group $parent )
	{
		$this->parent = $parent; return $this;
	}

		
	/**
	 * @return CoreApi\Entity\Image
	 */
	public function getImage()
	{
		return $this->image;
	}
	
	/**
	 * @return CoreApi\Entity\Group
	 */
	public function setImage(Image $image)
	{
		$this->image = $image;	return $this;
	}
	
	/**
	 * @return CoreApi\Entity\Group
	 */
	public function delImage()
	{
		$this->image = null;	return $this;
	}

}