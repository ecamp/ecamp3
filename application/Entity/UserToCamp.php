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
 * @Table(name="UserToCamp")
 */
class UserToCamp
{

	/**
	 * @Id @Column(type="integer")
	 * @GeneratedValue(strategy="AUTO")
	 * @var int
	 */
	private $id;


	/**
	 * @var User
	 *
	 * @ManyToOne(targetEntity="Entity\User")
	 */
	private $user;


	/**
	 * @var Camp
	 *
	 * @ManyToOne(targetEntity="Entity\Camp")
	 */
	private $camp;
	

	public function getId(){ return $this->id; }

	public function setCamp(Camp $camp){ $this->camp = $camp; }
	public function getCamp()          { return $this->camp; }

	public function setUser(User $user){ $this->user = $user; }
	public function getUser()          { return $this->user; }

}
