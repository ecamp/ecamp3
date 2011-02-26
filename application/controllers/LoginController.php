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
	extends Zend_Controller_Action
{

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;


	/**
	 * @var Zend_Session_Namespace
	 */
	private $authSession;


	public function init()
	{

		$this->view->headLink()->appendStylesheet('/css/layout.css');

		/** @var \Bisna\Application\Container\DoctrineController $doctrineContainer  */
		$doctrineContainer = Zend_Registry::getInstance()->get("doctrine");

		$this->em = $doctrineContainer->getEntityManager();


		$this->authSession = new Zend_Session_Namespace('Zend_Auth');
	}


	public function indexAction()
	{
		$logins = $this->em->getRepository("Entity\Login")->findAll();
		$this->view->logins = $logins;



		if(!is_null($this->authSession->Login))
		{
			$login = $this->em->find("\Entity\Login", $this->authSession->Login);
			$this->view->login = $login;
		}
		else
		{
			$this->view->login = null;
		}
	}


	public function loginAction()
	{
		$this->authSession->Login = null;


		$id = $this->getRequest()->getParam("EntityId");

		$login = $this->em->find("Entity\Login", $id);

		$this->authSession->Login = $login->GetId();


		$this->view->login = $login;
	}


	public function logoutAction()
	{
		$this->authSession->Login = null;


		$this->_forward("index");
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
