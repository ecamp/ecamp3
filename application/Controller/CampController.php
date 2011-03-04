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


class CampController extends \Controller\BaseController
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
     * @var Service\UserService
     * @Inject \Service\UserService
     */
	private $userService;

	/**
	 * @var Zend_Session_Namespace
	 */
	private $authSession;


    public function init()
    {
	    parent::init();
	    
		$this->view->headLink()->appendStylesheet('/css/layout.css');

		$this->authSession = new Zend_Session_Namespace('Zend_Auth');

        if(is_null($this->authSession->Login))
		{
			$this->_forward("index", "login");
			return;
		}
	}


    public function indexAction()
    {
		$login = $this->loginRepo->find($this->authSession->Login);

		$this->view->myUserCamps = $login->GetUser()->GetCamps();
		$this->view->allCamps = $this->campRepo->findAll();
    }


	public function editAction()
	{
        $id = $this->getRequest()->getParam("camp");

        if ($id == null)
        {
            throw new Exception('Id must be provided for the delete action');
        }
        
		$camp = $this->campRepo->find($id);
		$form = $camp->getForm();
		
		$form->setAction("/camp/save");
		$this->view->form = $form;
	}

    public function newAction()
    {
		$camp = new \Entity\Camp();
		$form = $camp->getForm();

		$form->setAction("/camp/create");
		$this->view->form = $form;
    }


	public function saveAction()
	{
		$id = $this->getRequest()->getParam("id");
		$camp = $this->em->find("Entity\Camp", $id);
		$form = $camp->getForm();

		if (!$camp->save($this->getRequest()->getParams())) 
		{
			$this->view->form = $camp->getForm();
			$this->render("edit");
			return;
		}

		$this->em->persist($camp);
		$this->em->flush();		
		$this->_redirect("/camp/index");
	}

    public function createAction()
    {
        $camp = new \Entity\Camp();
		$form = $camp->getForm();

		if (!$camp->save($this->getRequest()->getParams())) 
		{
			$this->view->form = $camp->getForm();
			$this->render("new");
			return;
		}

		$this->em->persist($camp);
		$this->em->flush();		
		$this->_redirect("/camp/index");
    }


	public function commitAction()
	{
		$login = $this->loginRepo->find($this->authSession->Login);
		$user = $login->getUser();
		$camp = $this->campRepo->find($this->getRequest()->getParam('camp'));

		$this->userService->addUserToCamp($user, $camp);

		$this->_redirect("/camp");
	}


	public function cancelfromcampAction()
	{

		$userCampId = $this->getRequest()->getParam('id');

		$userToCamp = $this->em->find("Entity\UserToCamp", $userCampId);

		if($userToCamp != null)
		{
			$this->em->remove($userToCamp);
			$this->em->flush();
		}

		$this->_redirect("/camp");
	}
}

