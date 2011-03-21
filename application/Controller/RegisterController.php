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
 
class RegisterController
	extends \Controller\BaseController
{

	/**
	 * @var \Doctrine\ORM\EntityRepository
	 * @Inject UserRepository
	 */
	private $userRepository;


	public function indexAction()
	{

		$registerForm = new \Form\Register();

		$registerForm->setDefaults($this->getRequest()->getParams());

		$this->view->registerForm = $registerForm;

	}


	public function registerAction()
	{
		$registerForm = new \Form\Register();

		if(!$registerForm->isValid($this->getRequest()->getParams()))
		{
			$this->view->registerForm = $registerForm;
			$this->render("index");
			return;
		}

		
		$mail = $registerForm->getValue('mail');
		

		/** @var $user \Entity\User */
		$user = $this->userRepository->findBy(array('mail' => $mail));

		if($user == null)
		{
			$user = new Entity\User();
			$user->setEmail($mail);
		}

		if($user->getState() != \Entity\User::STATE_NONREGISTERED)
		{
			throw new Exception("User with given MailAdress is already registered!");
		}

		$user->setUsername($registerForm->getValue('username'));
		$user->setScoutname($registerForm->getValue('scoutname'));
		$user->setFirstname($registerForm->getValue('firstname'));
		$user->setSurname($registerForm->getValue('surname'));


		$login = new Entity\Login();
		$login->setUser($user);
		$login->setPwd('testpw');

		$this->em->persist($login);
		$this->em->persist($user);
		$this->em->flush();


		$this->_redirect('/login');

	}

	public function isusernamefreeAction()
	{
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

		$username = $this->getRequest()->getParam('username');

		/** @var $user Entity\User */
		$user = $this->em->find('Entity\User', array('username' => $username));

		if(!is_null($user))
		{
			if(!is_null($user->getLogin()))
			{	return false;	}

			//if(!is_null($user->getOAuth()))
			//{	return false;}
			
		}

		return true;

	}

}
