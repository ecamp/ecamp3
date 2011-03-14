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

namespace Entity;

/**
 * @Entity
 * @Table(name="camps")
 */
class Camp extends BaseEntity
{
	public function __construct()
    {
		$this->userCamps = new \Doctrine\Common\Collections\ArrayCollection();
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
	 * @OneToOne(targetEntity="Entity\User")
	 * @JoinColumn(name="creator_id", referencedColumnName="id", nullable=false)
	 */
	private $creator;

	/**
	 * @var Group
	 * @ManyToOne(targetEntity="Group", inversedBy="camps")
	 */
	private $group;

	/**
	 * @var ArrayObject
	 * @OneToMany(targetEntity="Entity\UserCamp", mappedBy="camp")
	 */
	private $userCamps;

	/**
	 * @OneToMany(targetEntity="Period", mappedBy="camp")
	 */
	private $periods;


	public function getId(){ return $this->id; }

	public function setName($name){ $this->name = $name; }
	public function getName()     { return $this->name; }

	public function setTitle($title){ $this->title = $title; }
	public function getTitle()       { return $this->title; }

	public function setCreator(User $creator){ $this->creator = $creator; }
	public function getCreator()             { return $this->creator; }

	public function setGroup(Group $group){ $this->group = $group; }
	public function getGroup()             { return $this->group; }

	public function getPeriods() { return $this->periods; }
	
	public function getMembers()
    {
	    $members = new \Doctrine\Common\Collections\ArrayCollection();

		foreach($this->userCamps as $userCamp) {
			if($userCamp->isMember()) {
				$members->add($userCamp->getUser());
			}
		}

		return $members;
	}


}
