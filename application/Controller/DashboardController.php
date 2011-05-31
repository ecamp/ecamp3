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


class DashboardController extends \Controller\BaseController
{
	/**
     * @var Service\UserService
     * @Inject Service\UserService
     */
	private $userService;
	
	
    public function init()
    {
	    parent::init();
		
		if(!isset($this->me))
		{
			$this->_redirect("login");
			return;
		}
		
		/* later, the navigation should probably go out of the controller
		   to a more global position (XML file -> Bootstrap) */
		   
		$pages = array(
			array(
			'label'      => 'Dashboard',
			'title'      => 'Dashboard',
			'controller' => 'dashboard',
			'action'     => 'index',
			'pages' => array(
				
				array(
				'label'      => 'subitem',
				'title'      => 'subitem for nothing else than for testing whether the menu shows it or not',
				'controller' => 'dashboard',
				'action'     => 'subitem'),
				
				array(
				'label'      => 'subitem2',
				'title'      => 'subitem for nothing else than for testing whether the menu shows it or not',
				'controller' => 'dashboard',
				'action'     => 'subitem2')
				
				)),
			
			array(
			'label'      => 'Camps',
			'title'      => 'Camps',
			'controller' => 'dashboard',
			'action'     => 'camps'),
			
			array(
			'label'      => 'Friends',
			'title'      => 'Friends',
			'controller' => 'dashboard',
			'action'     => 'friends'),
			
			array(
			'label'      => 'Groups',
			'title'      => 'Groups',
			'controller' => 'dashboard',
			'action'     => 'groups'));
		
		$container = new Zend_Navigation($pages);
		$this->view->getHelper('navigation')->setContainer($container);
		$this->view->subnavi = $this->view->navigation()->menu()->renderMenu(NULL, array('onlyActiveBranch' => 1, 'renderParents' => 0,'minDepth'=> 1, 'maxDepth' => 2));
    }


    public function indexAction()
    {
		$friendshipRequests = $this->userService->getFriendshipInvitationsOf($this->me);
		$membershipRequests = $this->userService->getMembershipRequests($this->me);
		
		$requests = new Doctrine\Common\Collections\ArrayCollection;
		
		foreach( $friendshipRequests as $user )
		{
			$item = array();
			$item['isFriendshipRequest'] = 1;
			$item['user'] = $user;
			
			$requests->add($item);
		}
			
		foreach( $membershipRequests as $usergroup )
		{
			$item = array();
			$item['isMembershipRequest'] = 1;
			$item['usergroup'] = $usergroup;
			
			$requests->add($item);
		}
		
		$this->view->requests = $requests;	
    }
	
	public function campsAction() {}
	
	public function friendsAction() {
		/** load friends */
		$this->view->friends = $this->userService->getFriendsOf($this->me);
		
		/** load all users */
		$query = $this->em->getRepository("Entity\User")->createQueryBuilder("u");
		
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
		
		$this->view->rootgroups  = $this->em->getRepository("Entity\Group")->createQueryBuilder("g")
				->where("g.parent IS NULL ")
				->getQuery()
				->getResult();
				
	}
	
	public function subitemAction() { $this->render('camps'); }
	public function subitem2Action() { $this->render('camps'); }
}