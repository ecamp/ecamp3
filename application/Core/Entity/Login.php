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
 * @Table(name="logins")
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
	private $password;


	/**
	 * @Column(type="string", length=64)
	 */
	private $salt;

	
	/**
	 * @var \Models\User
	 * @OneToOne(targetEntity="User", mappedBy="login")
	 * @JoinColumn(name="user_id", referencedColumnName="id")
	 */
	private $user;



	public function getId(){ return $this->id; }

	
	public function setUser(User $user){ $this->user = $user; }
	public function getUser()          { return $this->user; }


	public function setNewPassword($password)
	{
		$this->salt = hash('sha256', uniqid(microtime(true), true));

		$password .= $this->salt;
		$this->password = hash('sha256', $password);
	}

	
	public function checkPassword($password)
	{
		$password = hash('sha256', $password . $this->salt);

		return ($password == $this->password);
	}
}
