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

namespace EcampCore\Service;

use EcampCore\Entity\User;
use EcampCore\Auth\AuthenticationService;
use EcampLib\Service\ServiceBase;

/**
 * @method EcampCore\Service\SupportService Simulate
 */
class SupportService 
	extends ServiceBase
{
	
	
	public function SupportUser(User $user){
		$this->aclRequire($this->me(), $user, 'SupportService::SupportUser');
		
		$auth = new AuthenticationService();
		$auth->replaceIdentity($user->getId());
	}
	
	public function StopUserSupport(){
		$auth = new AuthenticationService();
		$auth->restoreIdentity();
	}
	
}
