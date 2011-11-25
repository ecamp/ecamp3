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


use \Core\Entity\UserCamp;

class WebApp_LeaderController extends \WebApp\Controller\BaseController
{
	/**
	 * @var \Entity\Repository\CampRepository
	 * @Inject CampRepository
	 */
	private $campRepo;

    /**
     * @var Entity\Repository\LoginRepository
     * @Inject LoginRepository
     */
    private $loginRepo;
	
	/**
     * @var CoreApi\Service\UserService
     * @Inject CoreApi\Service\UserService
     */
	private $userService;

	
	
	/**
	 * @var \Entity\Camp
	 */
	private $camp;
	
	/**
	 * @var \Entity\Group
	 */
	private $group;
	
	/**
	 * @var \Entity\User
	 */
	private $owner;
	
	

	public function init()
    {
	    parent::init();

        if(!isset($this->me))
		{
			$this->_forward("index", "login");
			return;
		}
		
		
		/* load camp */
	    $campid = $this->getRequest()->getParam("camp");
	    $this->camp = $this->em->getRepository("Core\Entity\Camp")->find($campid);
	    $this->view->camp = $this->camp;
	    
	    /* load group */
	    $groupid = $this->getRequest()->getParam("group");
	    $this->group = $groupid ? $this->em->getRepository("Core\Entity\Group")->find($groupid) : null;
	    $this->view->group = $this->group;
		
	    /* load user */
	    $userid = $this->getRequest()->getParam("user");
	    $this->owner = $userid ? $this->em->getRepository("Core\Entity\User")->find($userid) : null;
	    $this->view->owner = $this->owner;
	    
	    
	    $this->setNavigation(new \WebApp\Navigation\Camp($this->camp));
	    
		
		/* move this to bootsrap */
		$event = new \WebApp\Plugin\StrategyEventListener($this->view, $this->em);
		$this->em->getEventManager()->addEventSubscriber($event);
	}

	
	
	public function indexAction()
	{
		$this->view->managers = $this->camp->getUsercamps()->filter(UserCamp::RoleFilter(UserCamp::ROLE_MANAGER));		
		$this->view->leaders  = $this->camp->getUsercamps()->filter(UserCamp::RoleFilter(UserCamp::ROLE_NORMAL));
		$this->view->guests   = $this->camp->getUsercamps()->filter(UserCamp::RoleFilter(UserCamp::ROLE_GUEST));
	}
	
}

