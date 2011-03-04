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
 * @Table(name="Login")
 */
class Login extends BaseEntity
{


	/**
	 * @Id @Column(type="integer")
	 * @GeneratedValue(strategy="AUTO")
	 * @var int
	 */
	private $id;


	/**
	 * @Column(type="string")
	 * @var string
	 */
	private $login;

	/**
	 * @var \Models\User
	 * @OneToOne(targetEntity="Entity\User", mappedBy="login")
	 * @JoinColumn(name="user_id", referencedColumnName="id")
	 */
	private $user;



	public function getId(){ return $this->id; }

	public function setLogin($login){ $this->login = $login; }
	public function getLogin()      { return $this->login; }

	public function setUser(User $user){ $this->user = $user; }
	public function getUser()          { return $this->user; }
}
