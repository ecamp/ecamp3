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


class WebApp_LoginController extends WebApp\Controller\BaseController
{

	/**
	 * @deprecated
	 * @var Core\Repository\UserRepository
	 * @Inject Core\Repository\UserRepository
	 */
	private $userRepo;

	/**
	 * @var CoreApi\Service\LoginService
	 * @Inject CoreApi\Service\LoginService
	 */
	private $loginService;


	public function indexAction()
	{
		if (!is_null($this->me))
		{
			$this->_forward('index', 'dashboard');
			$this->view->browserUrl();
		}

		$loginForm = new \WebApp\Form\Login();
		$loginForm->setAction("/login/login");
		$loginForm->setDefaults($this->getRequest()->getParams());

		$this->view->loginForm = $loginForm;

		//TODO: Remove this in Release
		$this->view->userlist = $this->userRepo->findAll();

	}


	public function loginAction()
	{
		$loginForm = new \WebApp\Form\Login();

		if (!$loginForm->isValid($this->getRequest()->getParams()))
		{
			$this->_forward('index');
			$this->view->browserUrl();
		}


		if ($this->checkLogin($loginForm->getValues()))
		{
			$this->_forward('index', 'dashboard');
			$this->view->browserUrl();
		}
		else
		{
			$this->_forward('index');
			$this->view->browserUrl();
		}
	}


	public function logoutAction()
	{
		$this->loginService->Logout();

		$this->_redirect("login");
	}


	protected function checkLogin($values)
	{
		$result = $this->loginService->Login($values['login'], $values['password']);

		//$this->view->message = $result->getMessages();

		return Zend_Auth::getInstance()->hasIdentity();
	}

	public function bypassAction()
	{
		$user = $this->userRepo->find($this->getRequest()->getParam('user'));
		$authAdapter = new \Core\Auth\Bypass($user);
		$result = Zend_Auth::getInstance()->authenticate($authAdapter);

		$this->_forward('index', 'dashboard');
		$this->view->browserUrl();
	}

}
