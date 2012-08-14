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

namespace Core\Job;

use CoreApi\Entity\User;
use CoreApi\Entity\Job;


/**
 * @method static Job name()
 */
class RegisterJobs 
{
	/**
	 * @param User $user
	 * @param string $activationCode
	 * @return Job
	 */
	public static function SendActivationCode(User $user, $activationCode){
		$job = new Job();
		$job->setClass(__CLASS__);
		$job->setJob(__FUNCTION__ . "_Job");
		$job->setParams($user->getId(), $activationCode);
		$job->setDescription("Sends a Mail with the ActivationCode for a new User");
	
		return $job;
	}
	
	
	public function SendActivationCode_Job($user_id, $activationCode)
	{
		// TODO
	}
	
	
}