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


class WebApp_GroupController extends \WebApp\Controller\BaseController
{
	
	/**
	 * @var Core\Service\CampService
     * @Inject Core\Service\CampService
	 */
	private $campService;
	
	/**
	 * @var Core\Service\GroupService
     * @Inject Core\Service\GroupService
	 */
	private $groupService;
	
	/**
	 * @var Core\Service\SearchUserService
	 * @Inject Core\Service\SearchUserService
	 */
	private $searchUserService;
	
	
	
    public function init()
    {
		parent::init();

		if(!isset($this->me))
		{
			$this->_redirect("login");
			return;
		}

	     /* load group */
	    $groupid = $this->getRequest()->getParam("group");
	    $this->group = $this->em->getRepository("Core\Entity\Group")->find($groupid);
	    $this->view->group = $this->group;


	    $this->setNavigation(new \WebApp\Navigation\Group($this->group));
    }

    public function showAction()
    {
		$this->view->membershipRequests = $this->me->isManagerOf($this->group) ? 
			$this->groupService->getMembershipRequests($this->group) : null;
		
    }
	
	public function membersAction()
	{
	}

	public function campsAction(){
	}

	public function deletecampAction(){
		$id = $this->getRequest()->getParam("id");
	    $camp = $this->em->getRepository("Core\Entity\Camp")->find($id);
		
	    $this->em->remove($camp);
		$this->em->flush();

		$this->_helper->getHelper('Redirector')->gotoRoute(array('action'=>'camps', 'group' => $this->group->getId()), 'group');
	}

	public function newcampAction(){
		$form = new \WebApp\Form\Camp();
		
		$form->setDefaults($this->getRequest()->getParams());

		$this->view->form = $form;
	}

	public function createcampAction()
	{
		$form = new \WebApp\Form\Camp();
		$params = $this->getRequest()->getParams();
		
		if(!$form->isValid($params))
		{
			$this->view->form = $form;
			$this->render("newcamp");
			return;
		}
		
		try 
		{
			$this->campService->CreateCampForGroup($this->group, $this->me, $params);
			$this->_helper->getHelper('Redirector')->gotoRoute(array('action'=>'camps', 'group' => $this->group->getId()), 'group');
		}
		catch(Exception $e)
		{
			$form->getElement("name")->addError("Name has already been taken.");
			
			$this->view->form = $form;
			$this->render("newcamp");
		}
	}
	
	
	public function searchuserAction()
	{
		$search = new \WebApp\Form\Search();
		$query = "";
		$users = array();
		
		if($search->isValid($this->getRequest()->getParams()))
		{	$query = $search->getValue('query');	}
			
		if($query != "")
		{	$users = $this->searchUserService->SearchForUser($query);	}
		
		$this->view->search = $search;
		$this->view->users = $users;
	}

	
	/** membership actions */
	
	public function requestAction(){

		$this->me->sendMembershipRequestTo($this->group);
		
		$this->em->flush();
		$this->_helper->flashMessenger->addMessage(array('info' => $this->t->translate("Your request has been sent to the group managers.")));
		$this->_helper->getHelper('Redirector')->gotoRoute(array('action'=>'show', 'group' => $this->group->getId()), 'group');
	}
	
	public function inviteAction()
	{
		$id = $this->getRequest()->getParam("user");
		$user = $this->em->getRepository("Core\Entity\User")->find($id);
		
		$this->groupService->inviteUserToGroup($user, $this->group, $this->me);
		
		$this->em->flush();
		$this->_helper->flashMessenger->addMessage(array('info' => $this->t->translate("Your invitation has been sent to the user.")));
		$this->_helper->getHelper('Redirector')->gotoRoute(array('action'=>'members', 'group' => $this->group->getId()), 'group');
	}

	public function leaveAction(){
		
		$this->me->deleteMembershipWith($this->group);
		
		$this->em->flush();
		$this->_helper->flashMessenger->addMessage(array('info' => $this->t->translate('Your not a member of this group anymore.')));
		$this->_helper->getHelper('Redirector')->gotoRoute(array('action'=>'show', 'group' => $this->group->getId()), 'group');
	}

	public function withdrawAction(){
		$this->me->deleteMembershipWith($this->group);

		$this->em->flush();
		$this->_helper->flashMessenger->addMessage(array('info' => $this->t->translate('Your request has been withdrawn.')));
		$this->_helper->getHelper('Redirector')->gotoRoute(array('action'=>'show', 'group' => $this->group->getId()), 'group');
	}

	public function kickoutAction()
	{
		$userid = $this->getRequest()->getParam("user");
		$user   = $this->em->getRepository("Core\Entity\User")->find($userid);

		if( $this->me->isManagerOf($this->group) )
			$user->deleteMembershipWith($this->group);

		$this->em->flush();
		$this->_helper->flashMessenger->addMessage(array('success' => $this->t->translate('%1$s is not a member of %2$s anymore.', $user->getUsername(), $this->group->getName())));
		$this->_helper->getHelper('Redirector')->gotoRoute(array('action'=>'members', 'group' => $this->group->getId(), 'user'=>null ), 'group');
	}
	
	public function acceptAction()
	{
		$id = $this->getRequest()->getParam("id");
		$usergroup = $this->em->getRepository("Core\Entity\UserGroup")->find($id);
		
		if($usergroup->isOpenRequest())
		{	$this->groupService->acceptMembershipRequest($this->me, $usergroup);	}
		
		if($usergroup->isOpenInvitation())
		{	$this->groupService->acceptMembershipInvitation($this->me, $usergroup);	}
		
		$this->em->flush();
		$this->_helper->flashMessenger->addMessage(array('success' => $this->t->translate('%1$s is now a member of %2$s.', $usergroup->getUser()->getUsername(), $usergroup->getGroup()->getName())));
		$this->_helper->getHelper('Redirector')->gotoRoute(array('action'=>'members', 'group' => $this->group->getId(), 'user'=>null ), 'group');
	}
	
	public function refuseAction()
	{
		$id = $this->getRequest()->getParam("id");
		$usergroup = $this->em->getRepository("Core\Entity\UserGroup")->find($id);
		
		if($usergroup->isOpenRequest())
		{	$this->groupService->refuseMembershipRequest($this->me, $usergroup);	}
		
		if($usergroup->isOpenInvitation())
		{	$this->groupService->refuseMembershipInvitation($this->me, $usergroup);	}
		
		
		$this->em->flush();
		$this->_helper->flashMessenger->addMessage(array('info' => $this->t->translate('The membership request has been refused.')));
		$this->_helper->getHelper('Redirector')->gotoRoute(array(), 'general');
	}

}
