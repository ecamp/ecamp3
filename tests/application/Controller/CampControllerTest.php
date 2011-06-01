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

class CampControllerTest extends EcampControllerTestCase
{

	public function setUp()
	{
		parent::setup();
	}

	public function tearDown()
	{
		parent::tearDown();
	}

	public function testTrue(){
		$this->assertTrue(true);
	}

	public function testController(){
		$u = new Entity\User();

		$l = new Entity\Login();
		$l->setUser($u);
		$l->setPwd("dummy");

		$this->em->persist($u);
		$this->em->persist($l);
		$this->em->flush();

		$this->authSession = new \Zend_Session_Namespace('Zend_Auth');
		$this->authSession->Login = 1;

		$this->dispatch('/dashboard');

		$this->assertController('dashboard');
		$this->assertAction('index');
		$this->assertResponseCode(200);
	}

}

