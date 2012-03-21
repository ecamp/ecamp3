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
	 * @var Core\Repository\UserRepository
	 * @Inject Core\Repository\UserRepository
	 */
	private $userRepo;


	/**
	 * @var CoreApi\Service\UserService
	 * @Inject CoreApi\Service\UserService
	 */
	private $userService;
	
	
	/**
	 * @var CoreApi\Service\RegisterService
	 * @Inject CoreApi\Service\RegisterService
	 */
	private $registerService;
	
	
	

	public function indexAction()
	{		
		$registerForm = new \WebApp\Form\Register();

		if($id = $this->getRequest()->getParam('id'))
		{
			/** @var $user \Core\Entity\User */
			$user = $this->userService->get($id);

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
		$registerForm->populate($params);

		
		try
		{
			$user = $this->registerService->Register($registerForm);
		}
		catch (\Core\Service\ValidationException $e)
		{
			$this->view->registerForm = $registerForm;
			$this->render("index");
			return;
		}
	}


	public function activateAction()
	{
		$id = $this->getRequest()->getParam('id');
		$key = $this->getRequest()->getParam('key');


		if($this->registerService->Activate($id, $key))
		{
			$this->forward('web+general', 'index', 'login');
		}
		else
		{
			/** @var $user \Entity\User */
			$user = $this->userRepo->find($id);

			$ac = $user->createNewActivationCode();
			$this->em->flush();
			die($ac);
		}

	}

}
