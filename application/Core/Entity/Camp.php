<?php
/*
 * Copyright (C) 2011 Pirmin Mattmann
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

namespace Core\Entity;

/**
 * @Entity
 * @Table(name="camps",
 *   uniqueConstraints={@UniqueConstraint(name="group_name_unique",columns={"group_id", "name"}),
 *                      @UniqueConstraint(name="owner_name_unique",columns={"owner_id", "name"})}
 *   )
 */
class Camp extends BaseEntity
{
	public function __construct()
	{
		$this->userCamps = new \Doctrine\Common\Collections\ArrayCollection();
		$this->events    = new \Doctrine\Common\Collections\ArrayCollection();
	}


	/**
	 * @return \CoreApi\Entity\Camp
	 */
	public function asReadonly()
	{
		return new \CoreApi\Entity\Camp($this);
	}

	/**
	 * @var int
	 * @Id @Column(type="integer")
	 * @GeneratedValue(strategy="AUTO")
	 */
	private $id;


	/**
	 * Short identifier, unique inside group or user
	 * @var string
	 * @Column(type="string", length=32, nullable=false )
	 */
	private $name;


	/**
	 * @var string
	 * @Column(type="string", length=64, nullable=false )
	 */
	private $title;


	/**
	 * @var User
	 * @ManyToOne(targetEntity="User")
	 * @JoinColumn(name="creator_id", referencedColumnName="id", nullable=false)
	 */
	private $creator;

	/**
	 * @var User
	 * @ManyToOne(targetEntity="User", inversedBy="mycamps")
	 */
	private $owner;

	/**
	 * @var Group
	 * @ManyToOne(targetEntity="Group", inversedBy="camps")
	 */
	private $group;

	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 * @OneToMany(targetEntity="UserCamp", mappedBy="camp")
	 */
	private $usercamps;

	/**
	 * @OneToMany(targetEntity="Period", mappedBy="camp")
	 */
	private $periods;

	/**
	 * @OneToMany(targetEntity="Event", mappedBy="camp")
	 */
	private $events;

	
	/** @Method() */
	public function getId()
	{
		return $this->id;
	}

	public function setName($name)
	{
		$this->name = $name;
	}
	/** @Method() */
	public function getName()
	{
		return $this->name;
	}

	public function setTitle($title)
	{
		$this->title = $title;
	}
	/** @Method() */
	public function getTitle()
	{
		return $this->title;
	}

	public function setCreator(User $creator)
	{
		$this->creator = $creator;
	}
	
	/**
	 * @MethodEntity()
	 * @return \Core\Entity\User  
	 */
	public function getCreator()
	{
		return $this->creator;
	}

	public function setGroup(Group $group)
	{
		$this->owner = null; $this->group = $group;
	}
	
	/**
	 * @MethodEntity()
	 * @return \Core\Entity\Group
	 */
	public function getGroup()
	{
		return $this->group;
	}

	public function setOwner(User $owner)
	{
		$this->group = null; $this->owner = $owner;
	}

	/**
	 * @MethodEntity()
	 * @return \Core\Entity\User  
	 */
	public function getOwner()
	{
		return $this->owner;
	}
		
	/** @Method() */
	public function belongsToUser()
	{
		return isset($this->owner);
	}
	
	/** 
	 * @MethodEntityList(type = "\CoreApi\Entity\Period")
	 * @return array
	 */
	public function getPeriods()
	{
		return $this->periods;
	}
	
	/**
	 * @MethodEntityList(type = "\CoreApi\Entity\Event")
	 * @return array
	 */
	public function getEvents()
	{
		return $this->events;
	}

	/**
	 * @MethodEntityList(type = "\CoreApi\Entity\UserCamp")
	 * @return \Doctrine\Common\Collections\ArrayCollection 
	 */
	public function getUserCamps()
	{
		return $this->usercamps;
	}


	/** @Method() */
	public function getRange()
	{
		if($this->getPeriods()->count() == 0)
		{
			return "-";
		}

		return $this->getPeriods()->first()->getStart()->format("d.m.Y") . ' - ' . $this->getPeriods()->last()->getEnd()->format("d.m.Y");
	}

	
	/**
	 * @MethodEntityList(type = "\CoreApi\Entity\User")
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getMembers()
	{
		$members = new \Doctrine\Common\Collections\ArrayCollection();

		foreach($this->usercamps as $userCamp) {
			if($userCamp->isMember()) {
				$members->add($userCamp->getUser());
			}
		}

		return $members;
	}
	
	/**
	 * @Method()
	 */
	public function isManager(User $user)
	{
		$closure = function($key, $element) use ($user)
		{
			return  $element->getRole() == UserCamp::ROLE_MANAGER && $element->getUser() == $user;
		};
	
		return $this->getUserCamps()->exists( $closure );
	}
	
	/**
	 * @Method()
	 */
	public function isMember(User $user)
	{
		$closure = function($key, $element) use ($user)
		{
			return  $element->getRole() == UserCamp::ROLE_MEMBER && $element->getUser() == $user;
		};
	
		return $this->getUserCamps()->exists( $closure );
	}


}
