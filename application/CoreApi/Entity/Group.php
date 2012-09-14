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

namespace CoreApi\Entity;

/**
 * @Entity
 * @Table(name="groups", indexes={@index(name="group_name_idx", columns={"name"})}, uniqueConstraints={@UniqueConstraint(name="group_parent_name_unique",columns={"parent_id","name"})})
 */
class Group extends BaseEntity
{
	public function __construct()
	{
		parent::__construct();
		
		$this->children = new \Doctrine\Common\Collections\ArrayCollection();
		$this->userGroups = new \Doctrine\Common\Collections\ArrayCollection();
		$this->camps = new \Doctrine\Common\Collections\ArrayCollection();
	}


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
	 * @OrderBy({"name" = "ASC"})
	 */
	private $children;

	/**
	 * @Column(type="string", length=64, nullable=false )
	 */
	private $description;

	/**
	 * @var Doctrine\Common\Collections\ArrayCollection
	 * @OneToMany(targetEntity="UserGroup", mappedBy="group", cascade={"all"}, orphanRemoval=true )
	 */
	private $userGroups;

	/**
	 * @var Doctrine\Common\Collections\ArrayCollection
	 * @OneToMany(targetEntity="Camp", mappedBy="group")
	 */
	private $camps;

	/**
	 * @var CoreApi\Entity\Image
	 * @OneToOne(targetEntity="Image")
	 * @JoinColumn(name="image_id", referencedColumnName="id")
	 */
	private $image;

	
	
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
	 * @return Group
	 */
	public function getParent()
	{
		return $this->parent;
	}
	
	public function setParent( $parent )
	{
		$this->parent = $parent; return $this;
	}

	
	/**
	 * @return array
	 */
	public function getChildren()
	{
		return $this->children;
	}
	
	public function hasChildren()
	{
		return ! $this->children->isEmpty();
	}

	
	/**
	 * @return array
	 */
	public function getUserGroups()
	{
		return $this->userGroups;
	}

	
	/**
	 * @return Camp
	 */
	public function getCamps()
	{
		return $this->camps;
	}

	
	/**
	 * @return array
	 */
	public function getMembers()
	{
		$members = new \Doctrine\Common\Collections\ArrayCollection();

		foreach($this->userGroups as $userGroup)
		{
			if($userGroup->isMember())
			{
				$members->add($userGroup->getUser());
			}
		}

		return $members;
	}

	
	/**
	 * @return array
	 */
	public function getPathAsArray()
	{
		$path = array();
		$group = $this;

		while( $group->getParent() != null )
		{
			$group = $group->getParent();
			$path[] = $group;
		}

		return array_reverse($path);
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

	
	
	public function isManager(User $user)
	{
		$closure = function($key, $element) use ($user)
		{
			return  $element->getRole() == UserGroup::ROLE_MANAGER && $element->getUser() == $user;
		};

		return $this->getUserGroups()->exists( $closure );
	}
	
	
	public function isMember(User $user)
	{
		$closure = function($key, $element) use ($user)
		{
			return  $element->getRole() == UserGroup::ROLE_MEMBER && $element->getUser() == $user;
		};
	
		return $this->getUserGroups()->exists( $closure );
	}

	
	public function acceptRequest($request, $manager) 
	{
		if( $this->isManager($manager) )
		$request->acceptRequest($manager);

		return $this;
	}

	
	public function refuseRequest($request, $manager) 
	{
		if( $this->isManager($manager) ) 
		{
			$request->getUser()->getUserGroups()->removeElement($request);
			$this->userGroups->removeElement($request);
		}

		return $this;
	}
}