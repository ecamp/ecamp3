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
		
		$this->loadDatabaseDump("empty.sql");
		$this->defineContext(null, null, null, null);
	}
	
	public function tearDown()
	{
		parent::tearDown();
	}
	
	
	private function getRegisterForm()
	{
		$registerForm = new Zend_Form();
		
		$registerForm->addElement(new Zend_Form_Element_Text('username'));
		$registerForm->getElement('username')->setValue('testuser');
		
		$registerForm->addElement(new Zend_Form_Element_Text('email'));
		$registerForm->getElement('email')->setValue('test@user.ch');
		
		$registerForm->addElement(new Zend_Form_Element_Text('scoutname'));
		$registerForm->getElement('scoutname')->setValue('scout-name');
		
		$registerForm->addElement(new Zend_Form_Element_Text('firstname'));
		$registerForm->getElement('firstname')->setValue('first-name');
		
		$registerForm->addElement(new Zend_Form_Element_Text('surname'));
		$registerForm->getElement('surname')->setValue('sur-name');
		
		$registerForm->addElement(new Zend_Form_Element_Text('password'));
		$registerForm->getElement('password')->setValue('password');
		
		$registerForm->addElement(new Zend_Form_Element_Text('passwordCheck'));
		$registerForm->getElement('passwordCheck')->setValue('password');
		
		return $registerForm;
	}
	
	public function testRegister()
	{
		$registerForm = $this->getRegisterForm();
		
		$user = $this->registerService->Register($registerForm);
		
		// Refresh UserEntity to see the LoginEntity
		$this->em->refresh($user);
		
		$this->assertEquals('testuser', $user->getUsername());
		$this->assertEquals('test@user.ch', $user->getEmail());
		$this->assertEquals('scout-name', $user->getScoutname());
		$this->assertEquals('sur-name', $user->getSurname());
		$this->assertEquals('first-name', $user->getFirstname());
		
		$this->assertTrue($user->getLogin()->checkPassword('password'));
	}
	
	
	public function testActivateUser()
	{
		$registerForm = $this->getRegisterForm();
		
		$user = $this->registerService->Register($registerForm);
		$activationCode = $user->createNewActivationCode();
		$this->em->flush();
		
		
/*
		$fail = $this->registerService->Activate($user->getId(), null);
		$this->assertFalse($fail);
*/	
		
		$success = $this->registerService->Activate($user->getId(), $activationCode);
		$this->assertTrue($success);
	}
	
}