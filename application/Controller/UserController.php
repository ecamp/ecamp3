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

class UserController extends \Controller\BaseController
{
	/**
     * @var Service\UserService
     * @Inject Service\UserService
     */
	private $userService;
	
	public function init()
	{
		parent::init();
	}
	
	public function showAction()
	{
		$id = $this->getRequest()->getParam("user");
		$this->view->user    = $this->em->getRepository("Entity\User")->find($id);
		$this->view->friends = $this->userService->getFriendsOf($this->view->user);
	}

	public function avatarAction()
	{
		/** todo: enable cache for dynamic avatars */
		
		$id = $this->getRequest()->getParam("user");
		
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

		$user = $this->em->getRepository("Entity\User")->find($id);
		
		if( $user->getImageData() == null ) {
			$this->_redirect('img/default_avatar.png');
		} else {
			$this->getResponse()->setHeader("Content-type", $user->getImageMime());
			$this->getResponse()->setBody($user->getImageData());
		}
		
		return;
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
		$this->_helper->getHelper('Redirector')->gotoRoute(array('action'=>'show', 'user' => $id), 'user');
	}
	
	public function ignoreAction()
	{
		$id = $this->getRequest()->getParam("user");
		$user = $this->em->getRepository("Entity\User")->find($id);
		
		$this->me->ignoreFriendshipRequestFrom($user);
		
		$this->em->flush();
		
		$this->_helper->getHelper('Redirector')->gotoRoute(array('action'=>'show', 'user' => $id), 'user');
	}
	
	public function divorceAction()
	{
		$id = $this->getRequest()->getParam("user");
		$user = $this->em->getRepository("Entity\User")->find($id);
		
		$this->me->divorceFrom($user);
		
		$this->em->flush();
		
		$this->_helper->getHelper('Redirector')->gotoRoute(array('action'=>'show', 'user' => $id), 'user');
	}
	
}