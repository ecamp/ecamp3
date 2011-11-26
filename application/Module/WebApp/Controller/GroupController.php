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
	 * @var CoreApi\Service\Group
     * @Inject CoreApi\Service\Group
	 */
	private $groupService2;
	
	/**
	 * @var CoreApi\Service\GroupService
     * @Inject CoreApi\Service\GroupService
	 */
	private $groupService;
	
	/**
	 * @var CoreApi\Service\SearchUserService
	 * @Inject CoreApi\Service\SearchUserService
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
	
	public function editcampAction()
	{	
		$camp = $this->groupService2->getCamp($this->group, $this->getRequest()->getParam("id") );
		
		$form = new \WebApp\Form\CampUpdate();
		$form->setData($camp);
		
		$this->view->form = $form;
	}
	
	public function updatecampAction()
	{
		$form = new \WebApp\Form\CampUpdate();
		$params = $this->getRequest()->getParams();
		
		try
		{
			/* we are not doing any validations here. the real validation is done in the service. however, this need to be here:
			 *  - for filters
			*  - for possible validations on WebApp-Level
			*/
			if(!$form->isValid($params))
				throw new \Ecamp\ValidationException();
		
			$this->groupService2->updateCamp($this->group, $params);
			$this->_helper->getHelper('Redirector')->gotoRoute(array('action'=>'camps', 'group' => $this->group->getId()), 'group');
		}
		
		/* catching permission exceptions might be outsourced to an upper level */
		catch(\Ecamp\PermissionException $e){
			die("You should not click on buttons you are not allowed to.");
		}
		
		/* oh snap, something went wrong. show the form again */
		catch(\Ecamp\ValidationException $e){
			if($e->form != null)
				$form->copyErrors( $e->form );
		
			$this->view->form = $form;
			$this->render("editcamp");
		}
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
		$form = new \WebApp\Form\CampCreate();
		
		$form->setDefaults($this->getRequest()->getParams());

		$this->view->form = $form;
	}

	public function createcampAction()
	{
		$form = new \WebApp\Form\CampCreate();
		$params = $this->getRequest()->getParams();
		
		try 
		{
			/* we are not doing any validations here. the real validation is done in the service. however, this need to be here:
			 *  - for filters
			 *  - for possible validations on WebApp-Level
			 */
			if(!$form->isValid($params))
				throw new \Ecamp\ValidationException();

			$this->groupService2->createCamp($this->group, $this->me, $params);
			$this->_helper->getHelper('Redirector')->gotoRoute(array('action'=>'camps', 'group' => $this->group->getId()), 'group');
		}
		
		/* catching permission exceptions might be outsourced to an upper level */
		catch(\Ecamp\PermissionException $e){
			die("You should not click on buttons you are not allowed to.");
		}
		
		/* oh snap, something went wrong. show the form again */
		catch(\Ecamp\ValidationException $e){
			if($e->form != null)
				$form->copyErrors( $e->form );

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
