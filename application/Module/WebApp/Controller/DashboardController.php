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


class WebApp_DashboardController extends \WebApp\Controller\BaseController
{
	
	/**
	* @var CoreApi\Service\User
	* @Inject CoreApi\Service\User
	*/
	private $userService;
	
	
	/**
	 * @var \Repository\UserRepository
	 * @Inject UserRepository
	 */
	private $userRepository;
	
	
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
		$friendshipRequests = $this->userRepository->findFriendshipInvitationsOf($this->me); // $this->userService->getFriendshipInvitationsOf($this->me);
		$membershipRequests = $this->userRepository->findMembershipRequestsOf($this->me); // $this->userService->getMembershipRequests($this->me);
		$membershipInvitations = $this->userRepository->findMembershipInvitations($this->me); // $this->userService->getMembershipInvitations($this->me);
		
				
		$this->view->friendshipRequests = new Doctrine\Common\Collections\ArrayCollection($friendshipRequests);
		$this->view->membershipRequests = new Doctrine\Common\Collections\ArrayCollection($membershipRequests);
		$this->view->membershipInvitations = new Doctrine\Common\Collections\ArrayCollection($membershipInvitations);
    }
	
	public function campsAction(){
	}

	public function deletecampAction(){
		$id = $this->getRequest()->getParam("id");
	    $camp = $this->em->getRepository("Core\Entity\Camp")->find($id);
		
	    $this->em->remove($camp);
		$this->em->flush();

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
			if(!$form->isValid($params))
				throw new \Ecamp\ValidationException();
	
			$this->userService->createCamp($this->me, $params);
			$this->_helper->getHelper('Redirector')->gotoRoute(array('action'=>'camps'));
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
	
	public function friendsAction() {
		/** load friends */
		$this->view->friends = $this->userRepository->findFriendsOf($this->me); // $this->userService->getFriendsOf($this->me);
		
		/** load all users */
		$query = $this->em->getRepository("Core\Entity\User")->createQueryBuilder("u");
		
		$adapter = new \Ecamp\Paginator\Doctrine($query);
		$paginator = new Zend_Paginator($adapter);
		$paginator->setItemCountPerPage( 21 );
		$paginator->setCurrentPageNumber( $this->getRequest()->getParam("page") );
		
		Zend_View_Helper_PaginationControl::setDefaultViewPartial('global/pagination_control.phtml');
		$paginator->setDefaultScrollingStyle('All');
		
		$this->view->paginator = $paginator;
	}
	
	public function groupsAction() {
		$this->view->memberships = $this->me->getMemberships();
		
		$this->view->rootgroups  = $this->em->getRepository("Core\Entity\Group")->createQueryBuilder("g")
				->where("g.parent IS NULL ")
				->getQuery()
				->getResult();
				
	}
	
	public function subitemAction() { $this->render('camps'); }
	public function subitem2Action() { $this->render('camps'); }
}
