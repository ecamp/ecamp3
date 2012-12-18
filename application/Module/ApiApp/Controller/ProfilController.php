<?php
/*
 * Copyright (C) 2012 Urban Suppiger
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

class ApiApp_ProfilController extends \ApiApp\Controller\BaseController
{	
	
	/**
	 * @var CoreApi\Service\UserService
	 * @Inject CoreApi\Service\UserService
	 */
	private $userService;

	
    public function init()
    {
		parent::init();
    }
    
    public function getAction()
    {	
    	$response = array();
    	$response["response"] = $this->serializeUser($this->me);
    	$response["request"] = $this->getRequest()->getParams();
    	
    	$this->getResponse()->setBody( Zend_Json::encode($response) );
    }
    
    public function updateAction()
    {
    	$this->contextProvider->set($this->me->getId(), null, null);
    	
    	$params = new \CoreApi\Service\Params\ArrayParams($this->getRequest()->getParams());
    	
    	$user = $this->userService->Update($params);
    
    	/**
			TODO: catch and forward validation errors
    	 */
    	
    	$response = array();
    	$response["response"] = $this->serializeUser($user);
    	$response["request"] = $this->getRequest()->getParams();
    	 
    	$this->getResponse()->setBody( Zend_Json::encode($response) );
    }
    
    private function serializeUser(\CoreApi\Entity\User $user){
    	
    	$profil = array();
    	$profil['id']        = $user->getId();
    	$profil['firstname'] = $user->getFirstname();
    	$profil['surname']   = $user->getSurname();
    	$profil['scoutname'] = $user->getScoutname();
    	$profil['birthday']  = $user->getBirthday();
    	
    	return $profil;
    }
}

