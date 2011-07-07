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
	 * @var Repository\UserRepository
	 * @Inject \Repository\UserRepository
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

		$this->view->userlist = $this->userRepository->findAll();
	}


	public function loginAction()
	{
		$loginForm = new \Form\Login();

		if(!$loginForm->isValid($this->getRequest()->getParams()))
		{	$this->_forward('index');	}


        if($this->checkLogin($loginForm->getValues()))
        {
        	$redirectUrl = $this->getRequest()->getParam('redirect', "");

        	// @todo: is the url valid/routable??
        	
        	$this->_redirect($redirectUrl);
        	exit;
        }
        else
        {	$this->_forward('index');	}

	}
	
	public function bypassAction()
	{
		$user = $this->userRepository->find($this->getRequest()->getParam('user'));
		$authAdapter = new \Logic\Auth\Bypass($user);
        	$result = Zend_Auth::getInstance()->authenticate($authAdapter);
	
        $this->_redirect("");
		exit;
	}


	public function logoutAction()
	{
        \Zend_Auth::getInstance()->clearIdentity();

		$this->_redirect("login");
		exit;
	}


    protected function checkLogin($values)
    {
        $authAdapter = new \Logic\Auth\Adapter($values['login'], $values['password']);
        $result = Zend_Auth::getInstance()->authenticate($authAdapter);

        $this->view->message = $result->getMessages();

        if(Zend_Auth::getInstance()->hasIdentity())
        {   return true;    }

        return false;
    }

    

}
