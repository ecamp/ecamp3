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

namespace PMod;


class UserToCampPMod
{

	/**
	 * @var \Entity\UserToCamp
	 */
	private $userToCamp;


	public function __construct(\Entity\UserToCamp $userToCamp)
	{
		$this->userToCamp = $userToCamp;
	}


	/**
	 * @return \Entity\User
	 */
	public function User()
	{
		return $this->userToCamp->getUser();
	}


	/**
	 * @return UserPMod
	 */
	public function UserPMod()
	{
		return new UserPMod($this->User());
	}


	/**
	 * @return \Entity\Camp
	 */
	public function Camp()
	{
		return $this->userToCamp->getCamp();
	}

	
	/**
	 * @return CampPMod
	 */
	public function CampPMod()
	{
		return new CampPMod($this->Camp());
	}


	public function CancelFromCampLink()
	{
		return "/camp/cancelFromCamp/" . $this->userToCamp->getId();
	}
}
