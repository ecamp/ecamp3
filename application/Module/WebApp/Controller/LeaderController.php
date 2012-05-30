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


use \CoreApi\Entity\UserCamp;

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
	 * @var \CoreApi\Entity\Camp
	 */
	private $camp;
	
	/**
	 * @var \CoreApi\Entity\Group
	 */
	private $group;
	
	/**
	 * @var \CoreApi\Entity\User
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

		$context = $this->contextProvider->getContext();
		
		/* load camp */
	    $this->camp = $context->getCamp();
	    $this->view->camp = $this->camp;

	    /* load group */
	    $this->group = $context->getGroup();
	    $this->view->group = $this->group;
		
	    /* load user */
	    $this->user = $context->getUser();
	    $this->view->owner = $this->user;
	    
	    
	    
	    $this->setNavigation(new \WebApp\Navigation\Camp($this->camp));
	    
	}

	
	
	public function indexAction()
	{
		
		$this->view->managers = $this->camp->getMembersByRoles(UserCamp::ROLE_MANAGER);
			// $this->camp->getUserCamps()->filter(UserCamp::RoleFilter(UserCamp::ROLE_MANAGER));		
		$this->view->leaders  = $this->camp->getMembersByRoles(UserCamp::ROLE_MEMBER);
			// $this->camp->getUserCamps()->filter(UserCamp::RoleFilter(UserCamp::ROLE_MEMBER));
		$this->view->guests   = $this->camp->getMembersByRoles(UserCamp::ROLE_GUEST);
			// $this->camp->getUserCamps()->filter(UserCamp::RoleFilter(UserCamp::ROLE_GUEST));
		
	}
	
}

