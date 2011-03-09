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

	/**
	 * @Id @Column(type="integer")
	 * @GeneratedValue(strategy="AUTO")
	 * @var int
	 */
	private $id;


	/**
	 * @var string
	 * @Column(type="string", length=32, nullable=false )
	 */
	private $name;


	/**
	 * @var string
	 * @Column(type="string", length=32, nullable=false )
	 */
	private $slogan;


	/**
	 * @var User
	 * @OneToOne(targetEntity="Entity\User")
	 * @JoinColumn(name="creator_id", referencedColumnName="id")
	 */
	private $creator;


	/**
	 * Page Object
	 * @var ArrayObject
	 *
	 * @OneToMany(targetEntity="Entity\UserCamp", mappedBy="camp")
	 */
	private $userCamp;  



	public function getId(){ return $this->id; }

	public function setName($name){ $this->name = $name; }
	public function getName()     { return $this->name; }

	public function setSlogan($slogan){ $this->slogan = $slogan; }
	public function getSlogan()       { return $this->slogan; }

	public function setCreator(User $creator){ $this->creator = $creator; }
	public function getCreator()             { return $this->creator; }


}
