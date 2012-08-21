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

use CoreApi\Service\Params\Params;

class RegisterServiceTest extends ServiceTestCase
{
	
	/**
	 * @var CoreApi\Service\RegisterService
	 * @Inject CoreApi\Service\RegisterService
	 */
	private $registerService;
	
	
	/**
	 * @var CoreApi\Service\LoginService
	 * @Inject CoreApi\Service\LoginService
	 */
	private $loginService;
	
	
	public function setUp()
	{
		parent::setUp();
		
		$this->defineContext(null, null, null, null);
	}
	
	private function getRegisterData()
	{
		return array
		(	'username' 		=> 'testuser'
		,	'email' 		=> 'test@user.ch'
		,	'scoutname'		=> 'scout-name'
		,	'firstname'		=> 'first-name'
		,	'surname'		=> 'sur-name'
		,	'password'		=> 'password'
		,	'passwordCheck'	=> 'password'
		);
	}
	
	public function testRegister()
	{
		$registerData = $this->getRegisterData();
		
		$user = $this->registerService->Register(Params::FromArray($registerData));
		
		// Refresh UserEntity to see the LoginEntity
		$this->em->refresh($user);
		
		$this->assertEquals($registerData['username'], 	$user->getUsername());
		$this->assertEquals($registerData['email'], 	$user->getEmail());
		$this->assertEquals($registerData['scoutname'], $user->getScoutname());
		$this->assertEquals($registerData['surname'], 	$user->getSurname());
		$this->assertEquals($registerData['firstname'], $user->getFirstname());
		
		$this->assertTrue($user->getLogin()->checkPassword('password'));
	}
	
	
	public function testActivateUser()
	{
		$registerData = $this->getRegisterData();
		
		$user = $this->registerService->Register(Params::FromArray($registerData));
		$activationCode = $user->createNewActivationCode();
		$this->em->flush();
		
		$success = $this->registerService->Activate($user->getId(), $activationCode);
		$this->assertTrue($success);
	}
	
}