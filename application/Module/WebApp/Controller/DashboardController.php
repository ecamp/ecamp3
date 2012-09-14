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

use CoreApi\Service\Params\Params;

class WebApp_DashboardController extends \WebApp\Controller\BaseController
{
	
	/**
	 * @var CoreApi\Service\UserService
	 * @Inject CoreApi\Service\UserService
	 */
	private $userService;
	
	/**
	 * @var CoreApi\Service\CampService
	 * @Inject CoreApi\Service\CampService
	 */
	private $campService;
	
	/**
	 * @var CoreApi\Service\GroupService
	 * @Inject CoreApi\Service\GroupService
	 */
	private $groupService;
	
	/**
	 * @var CoreApi\Service\RelationshipService
	 * @Inject CoreApi\Service\RelationshipService
	 */
	private $relationshipService;
	
	/**
	 * @var CoreApi\Service\MembershipService
	 * @Inject CoreApi\Service\MembershipService
	 */
	private $membershipService;
	
    public function init()
    {
	    parent::init();
		
		if(!isset($this->me))
		{
			$this->_redirect("login");
			return;
		}

		$this->setNavigation(new WebApp\Navigation\Dashboard());
		$this->view->subnavi = $this->view->navigation()->menu()->renderMenu(NULL, array('onlyActiveBranch' => 1, 'renderParents' => 0,'minDepth'=> 1, 'maxDepth' => 2));
    }


    public function indexAction()
    {
		//$friendshipRequests = $this->relationshipService->getOpenInvitations();
		//$membershipRequests = $this->membershipService->getMembershipRequests($this->me);
		//$membershipInvitations = $this->membershipService->getMembershipInvitations($this->me);
				
		$this->view->friendshipRequests = new Doctrine\Common\Collections\ArrayCollection();
		$this->view->membershipRequests = new Doctrine\Common\Collections\ArrayCollection();
		$this->view->membershipInvitations = new Doctrine\Common\Collections\ArrayCollection();
    }
	
	public function campsAction(){
	}
	
	public function deletecampAction(){
		$id = $this->getRequest()->getParam("id");
		 
		$this->userService->DeleteCamp($id);
	
		$this->_helper->getHelper('Redirector')->gotoRoute(array('action'=>'camps'));
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
			if( !$form->isValid($params))
			{
				throw new \Core\Service\ValidationException();
			}

			$camp = $this->campService->Create(Params::Create($form));
			
			$this->_helper->getHelper('Redirector')->gotoRoute(array('action'=>'camps'));
		}
		
		catch(\Core\Service\ValidationException $e){
			$this->view->form = $form;
			$this->render("newcamp");
			return;
		}
	}
	
	public function editcampAction()
	{
		$camp = $this->campService->Get( $this->getRequest()->getParam("id") );
	
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
			if( !$form->isValid($params))
			{
				throw new \Core\Service\ValidationException();
			}
	
			$this->userService->UpdateCamp($form);
				
			$this->_helper->getHelper('Redirector')->gotoRoute(array('action'=>'camps'), 'web+general');
		}
	
		/* oh snap, something went wrong. show the form again */
		catch(\Core\Service\ValidationException $e){
			$this->view->form = $form;
			$this->render("editcamp");
			return;
		}
	}
	
	public function friendsAction() {
		/** load friends */
		$this->view->friends = $this->friendService->Get();
		
		/** load all users */
		$paginator = $this->userService->GetPaginator();
		$paginator->setItemCountPerPage( 21 );
		$paginator->setCurrentPageNumber( $this->getRequest()->getParam("page") );
		
		Zend_View_Helper_PaginationControl::setDefaultViewPartial('global/pagination_control.phtml');
		$paginator->setDefaultScrollingStyle('All');
		
		$this->view->paginator = $paginator;
	}
	
	public function groupsAction() {
		$this->view->memberships = $this->me->getMemberships();
		
		$this->view->rootgroups  = $this->groupService->GetRoots();
				
	}
}
