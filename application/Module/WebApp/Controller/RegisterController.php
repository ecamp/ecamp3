<?php
/**
 *
 * Copyright (C) 2011 pirminmattmann
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
 *
 */
 
class WebApp_RegisterController
	extends \WebApp\Controller\BaseController
{

	/**
	 * @var \Core\Repository\UserRepository
	 * @Inject \Core\Repository\UserRepository
	 */
	private $userRepo;


	/**
	 * @var \CoreApi\Service\User
	 * @Inject CoreApi\Service\User
	 */
	private $userService;
	
	
	/**
	 * @var \CoreApi\Service\Login
	 * @Inject \CoreApi\Service\Login
	 */
	private $loginService;
	
	
	

	public function indexAction()
	{

		$registerForm = new \WebApp\Form\Register();

		if($id = $this->getRequest()->getParam('id'))
		{
			/** @var $user \Core\Entity\User */
			$user = $this->userRepo->find($id);

			if(!is_null($user) && $user->getState() == \Core\Entity\User::STATE_NONREGISTERED)
			{
				$registerForm->setDefault('email', 		$user->getEmail());
				$registerForm->setDefault('scoutname', 	$user->getScoutname());
				$registerForm->setDefault('firstname', 	$user->getFirstname());
				$registerForm->setDefault('surname', 	$user->getSurname());
			}
		}

		$registerForm->setDefaults($this->getRequest()->getParams());

		$this->view->registerForm = $registerForm;
	}


	public function registerAction()
	{
		$params = $this->getRequest()->getParams();
		
		$registerForm = new \WebApp\Form\Register();

		if(!$registerForm->isValid($params))
		{
			$this->view->registerForm = $registerForm;
			$this->render("index");
			return;
		}
		
		try
		{
			$email 		= $params['email'];
			$password 	= $params['password1'];
			
			$user 	= $this->userService->create($email, $params);
			$login 	= $this->loginService->create($user, $password);
		}
		catch (Exception $e)
		{
			$registerForm->getElement('email')->addError($e->getMessage());
			
			$this->view->registerForm = $registerForm;
			$this->render("index");
			return;
		}

		
		$activationCode = $user->createNewActivationCode();
		$this->em->flush();
		
		
		$link = "/register/activate/" . $user->getId() . "/key/" . $activationCode;
		echo "<a href='" . $link . "'>" . $link . "</a>";
		die();
	}


	public function activateAction()
	{
		$id = $this->getRequest()->getParam('id');
		$key = $this->getRequest()->getParam('key');


		if($this->userService->activate($id, $key))
		{
			$this->em->flush();

			$this->_forward('index', 'login');
		}
		else
		{
			/** @var $user \Entity\User */
			$user = $this->userRepo->find($id);

			die($user->createNewActivationCode());
		}

	}

}
