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
	 * @var CoreApi\Service\UserService
	 * @Inject CoreApi\Service\UserService
	 */
	private $userService;
	
	/**
	 * @var CoreApi\Service\LoginService
	 * @Inject CoreApi\Service\LoginService
	 */
	private $loginService;
	
	
	public function setUp()
	{
		parent::setUp();
		
// 		$this->loadDatabaseDump("loginServiceTest.sql");
// 		$this->defineContext(2, 2, 4, 5);
	}
	
	public function tearDown()
	{
		parent::tearDown();
	}
	
	
	public function te_stCreateLogin()
	{
		$user = $this->userService->Get(2);
		
		$this->loginService->Create($user, new Zend_Form());
// 		$this->assertTrue($resp->isError() == true);
	}
	
	
	
}