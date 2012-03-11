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

class LoginServiceTest extends ServiceTestCase
{
	
	
	/**
	 * @var CoreApi\Service\User\UserService
	 * @Inject CoreApi\Service\User\UserService
	 */
	private $userService;
	
	/**
	 * @var CoreApi\Service\Login\LoginService
	 * @Inject CoreApi\Service\Login\LoginService
	 */
	private $loginService;
	
	
	public function setUp()
	{
		parent::setUp();
	}
	
	public function tearDown()
	{
		parent::tearDown();
	}
	
	
	public function testCreateLogin()
	{
		$user = new Core\Entity\User();
		
		$resp = $this->loginService->Create($user, new Zend_Form());
		echo "test";
		
	}
	
	public function test2CreateLogin()
	{
	
		//$this->loginService->Create($user, $form);
		echo "test2";
	
	}
	
	
	
}