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

class DoctrineController extends \Controller\BaseController
{
	public function init()
	{
		parent::init();
	}


	public function indexAction()
	{
		$this->view->users =
			$this->em->getRepository('Entity\User')->findAll();

		$userForm = new Application_Form_User();
		$userForm->setAction('/doctrine/save');

		$this->view->userForm = $userForm;


		$userId = $this->getRequest()->getParam('id');

		if(isset($userId))
		{
			$user = $this->em->find('Entity\User', $userId);

			if($user instanceof Entity\User)
			{
				$userForm->setData($user);
			}
		}
	}


	public function saveAction()
	{

		$userForm = new Application_Form_User();

		if($userForm->isValid($this->getRequest()->getParams()))
		{
			$userId = $userForm->getId();

			if(isset($userId))
			{
				$user = $this->em->find('Entity\User', $userId);

				if($user instanceof Entity\User)
				{
					$userForm->grabData($user);

					$this->em->flush();
				}
			}

			$this->_redirect('/doctrine/index');
		}
		else
		{
			die("DataIsInvalid");
		}

	}


	public function adduserAction()
	{
		$user = new Entity\User();

		$this->em->persist($user);

		$this->em->flush();


		$this->_redirect('/doctrine/index');
	}

}