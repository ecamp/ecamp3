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
	 * @var \Core\Repository\UserRepository
	 * @Inject \Core\Repository\UserRepository
	 */
	private $userRepo;

	/**
	 * @var \CoreApi\Service\Login
	 * @Inject \CoreApi\Service\Login
	 */
	private $loginService;


	/**
	 * IndexAction
	 *
	 * It shows the LoginForm or forwards to the
	 * Dashboard, if User is already logged in.
	 */
	public function indexAction()
	{
		if (!is_null($this->me))
		{
			$this->_forward('index', 'dashboard');
			$this->view->browserUrl();
		}

		$loginForm = new \WebApp\Form\Login\Login();
		$loginForm->setAction("/login/login");
		$loginForm->setDefaults($this->getRequest()->getParams());

		$this->view->loginForm = $loginForm;

		//TODO: Remove this in Release
		$this->view->userlist = $this->userRepo->findAll();

	}


	/**
	 * LoginAction
	 *
	 * Logs a User in.
	 * If successful, it forward the User to Dashboard
	 * If failed, it forwards the user to indexAciton for retry
	 */
	public function loginAction()
	{
		$loginForm = new \WebApp\Form\Login\Login();

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


	/**
	 * LogoutAciton
	 *
	 * Logs a User out.
	 * Redirects the user to the index
	 */
	public function logoutAction()
	{
		$this->loginService->logout();

		$this->_redirect("login");
	}


	/**
	 * PasswordLostAction
	 *
	 * Provides a form which takes an eMailadrese.
	 * If the Form is submitted, a Mail with a Password-Reset-Link
	 * is sent (done by the Service).
	 */
	public function passwordlostAction()
	{
		$passwordLost = new \WebApp\Form\Login\PasswordLost();

		if ($this->getRequest()->isPost())
		{
			if($passwordLost->isValid($this->getRequest()->getParams()))
			{
				$resetKey = $this->loginService->forgotPassword($passwordLost->getValue('email'));
	
				if (!IS_PRODUCTION)
				{
					/* PostDispatch initiate the EntityManger to flash the changed data */
					$this->postDispatch();
					die($resetKey);
				}
	
				/*
				 * TODO: Send Mail with Password Reset Link
				 */
			}
		}

		$this->view->passwordLost = $passwordLost;
	}


	/**
	 * ResetPasswordAction
	 *
	 * Provides a Form to reset a Password.
	 * This works only, if a ResetKey is provided.
	 */
	public function resetpasswordAction()
	{
		$resetPassword = new \WebApp\Form\Login\ResetPassword();
		$resetPassword->populate($this->getRequest()->getParams());

		if ($this->getRequest()->isPost())
		{
			$resetKey = $resetPassword->getValue('resetKey');
			$password = $resetPassword->getValue('password1');

			/**
			 * Insert $resetPassword as argument, when new validation concept is accepted!
			 */
			$this->loginService->resetPassword($resetKey, $password);

			$this->_forward('index');

		}

		$this->view->resetPassword = $resetPassword;
	}


	/**
	 * Checks, whether the given data can authentificate a User
	 * (Array must contain keys for Login and Password)
	 *
	 * @param array $values
	 */
    private function checkLogin($values)
    {
    	$result = $this->loginService->login($values['login'], $values['password']);

		$this->view->message = $result->getMessages();

		if (Zend_Auth::getInstance()->hasIdentity())
		{
			return true;
		}

		return false;
	}


    /**
     * ByPassAction
     *
     * This is a hack for developing.
     * It logs a User in, just by providing a UserId;
     */
	public function bypassAction()
	{
		$user = $this->userRepo->find($this->getRequest()->getParam('user'));
		$authAdapter = new \CoreApi\Service\Auth\Bypass($user);
		$result = Zend_Auth::getInstance()->authenticate($authAdapter);

		$this->_forward('index', 'dashboard');
		$this->view->browserUrl();
	}

}
