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
     * @var Entity\Repository\LoginRepository
	 * @Inject LoginRepository
     */
    private $loginRepo;

	public function init()
	{
		parent::init();
	}

	public function indexAction()
	{
		$this->view->logins = $this->loginRepo->findAll();

		if(!is_null($this->authSession->Login))
		{
			$this->view->login = $this->loginRepo->find($this->authSession->Login);
		}
		else
		{
			$this->view->login = null;
		}
	}


	public function loginAction()
	{
		$this->authSession->Login = null;

		$id = $this->getRequest()->getParam("id");
		$login = $this->loginRepo->find($id);
		$this->authSession->Login = $login->getId();

		$this->view->login = $login;
		
		$this->view->me = $login->getUser();
	}


	public function logoutAction()
	{
		$this->authSession->Login = null;
		$this->_redirect("login");
	}


	public function identifyAction()
	{
		if($this->authSession->Login == null)
		{
			die("Not Authenticated");
		}

		die($this->authSession->Login);
	}

}
