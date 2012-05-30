<?php
/*
 * Copyright (C) 2011 Urban Suppiger
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

class WebApp_UserController extends \WebApp\Controller\BaseController
{

	/**
	 * @var CoreApi\Service\UserService
	 * @Inject CoreApi\Service\UserService
	 */
	private $userService;
	
	/**
	 * @var CoreApi\Service\FriendService
	 * @Inject CoreApi\Service\FriendService
	 */
	private $friendService;



	public function init()
	{
		parent::init();
	}

	public function showAction()
	{
		$user = $this->contextProvider->getContext()->getUser();
		
		$this->view->user    = $user;
		$this->view->friends = $this->friendService->Get($user);

		$this->view->userGroups  = $user->getAcceptedUserGroups();
		$this->view->userCamps   = $user->getAcceptedUserCamps();
	}

	/** Friendship actions */

	public function addAction()
	{
		$user = $this->contextProvider->getContext()->getUser();
		$this->friendService->request($user);
		$this->_helper->getHelper('Redirector')->gotoRoute(array('action'=>'show', 'user' => $user->getId()), 'web+user');
	}

	public function acceptAction()
	{
		$user = $this->contextProvider->getContext()->getUser();
		$this->friendService->accept($user);
		$this->_helper->getHelper('Redirector')->gotoRoute(array('action'=>'show', 'user' => $user->getId()), 'web+user');
	}

	public function ignoreAction()
	{
		$user = $this->contextProvider->getContext()->getUser();
		$this->friendService->reject($user);
		$this->_helper->getHelper('Redirector')->gotoRoute(array('action'=>'show', 'user' => $user->getId()), 'web+user');
	}

	public function divorceAction()
	{
		$user = $this->contextProvider->getContext()->getUser();
		$this->friendService->terminate($user);
		$this->_helper->getHelper('Redirector')->gotoRoute(array('action'=>'show', 'user' => $user->getId()), 'web+user');
	}
	
	public function editprofileAction()
	{
		$editprofileForm = new \WebApp\Form\EditProfile();

		if($id = $this->getRequest()->getParam('id'))
		{
			/** @var $user \CoreApi\Entity\User */
			$user = $this->userService->get($id);

			if(!is_null($user) && $user->getState() == \CoreApi\Entity\User::STATE_NONREGISTERED)
			{
				$editprofileForm->setDefault('username', 	$user->getUsername());
				$editprofileForm->setDefault('scoutname', 	$user->getScoutname());
			}
		}

		$editprofileForm->setDefaults($this->getRequest()->getParams());

		$this->view->editprofileForm = $editprofileForm;
	}
	
}