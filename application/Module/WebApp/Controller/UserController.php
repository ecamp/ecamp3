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
	 * @var \Repository\UserRepository
	 * @Inject UserRepository
	 */
	private $userRepository;

	
	
	public function init()
	{
		parent::init();
	}
	
	public function showAction()
	{
		$id = $this->getRequest()->getParam("user");

		/** @var $user \Entity\User */
		$user = $this->em->getRepository("Core\Entity\User")->find($id);

		$friendshipRequests = ($user == $this->me) ? 
			$friendshipRequests = $this->userRepository->findFriendshipInvitationsOf($this->me) : null;
		
		$this->view->user    = $user;
		$this->view->friends = $this->userRepository->findFriendsOf($this->view->user);
		$this->view->friendshipRequests = $friendshipRequests;

		$this->view->userGroups  = $user->getAcceptedUserGroups();
		$this->view->userCamps   = $user->getAcceptedUserCamps();
	}
	
	/** Friendship actions */
	
	public function addAction()
	{
		$id = $this->getRequest()->getParam("user");
		$user = $this->em->getRepository("Entity\User")->find($id);
		
		$this->me->sendFriendshipRequestTo($user);
		
		$this->em->flush();
		$this->_helper->getHelper('Redirector')->gotoRoute(array('action'=>'show', 'user' => $id), 'user');
	}
	
	public function acceptAction()
	{
		$id = $this->getRequest()->getParam("user");
		$user = $this->em->getRepository("Entity\User")->find($id);
		
		$this->me->acceptFriendshipRequestFrom($user);
		
		$this->em->flush();
		$this->_helper->getHelper('Redirector')->gotoRoute(array('action'=>'show', 'user' => $this->me->getId()), 'user');
	}
	
	public function ignoreAction()
	{
		$id = $this->getRequest()->getParam("user");
		$user = $this->em->getRepository("Core\Entity\User")->find($id);
		
		$this->me->ignoreFriendshipRequestFrom($user);
		
		$this->em->flush();
		
		$this->_helper->getHelper('Redirector')->gotoRoute(array('action'=>'show', 'user' => $id), 'user');
	}
	
	public function divorceAction()
	{
		$id = $this->getRequest()->getParam("user");
		$user = $this->em->getRepository("Core\Entity\User")->find($id);
		
		$this->me->divorceFrom($user);
		
		$this->em->flush();
		
		$this->_helper->getHelper('Redirector')->gotoRoute(array('action'=>'show', 'user' => $id), 'user');
	}
	
}