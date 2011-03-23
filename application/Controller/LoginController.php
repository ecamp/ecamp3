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


class LoginController
	extends \Controller\BaseController
{

	/**
	 * @var \Doctrine\ORM\EntityRepository
	 * @Inject UserRepository
	 */
	private $userRepository;


	public function indexAction()
	{
		if(!is_null($this->me))
		{
			$this->_forward('index', 'dashboard');
		}

		$loginForm = new \Form\Login();
		$loginForm->setAction("/login/login");
		$loginForm->setDefaults($this->getRequest()->getParams());

		$this->view->loginForm = $loginForm;
	}


	public function loginAction()
	{
		$loginForm = new \Form\Login();

		if(!$loginForm->isValid($this->getRequest()->getParams()))
		{	$this->_forward('index');	}


		$mailValidator = new \Zend_Validate_EmailAddress();
		
		$login 		= $loginForm->getValue('login');
		$password 	= $loginForm->getValue('password');

		
		/** @var $user \Entity\User */
		if($mailValidator->isValid($login))
		{	$user = $this->userRepository->findOneBy(array('email' => $login));	}
		else
		{	$user = $this->userRepository->findOneBy(array('username' => $login));	}


		if(
			!is_null($user) && !is_null($user->getLogin()) &&
			$user->getLogin()->checkPassword($password))
		{
			$this->authSession->Login = $user->getLogin()->getId();
			$this->_forward('index', 'dashboard');
			return;
		}

		$this->_forward('index');
	}


	public function logoutAction()
	{
		$this->authSession->Login = null;
		$this->_redirect("login");
	}

}
