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


class CampController extends Zend_Controller_Action
{
    /**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

    /**
     * @var Bisna\Application\Container\DoctrineContainer
     */
    protected $doctrine;

    /**
     * @var Entity\Repository\CampRepository
     */
    protected $campRepo;

    /**
     * @var Entity\Repository\CampRepository
     */
    protected $loginRepo;

	/**
	 * @var Zend_Session_Namespace
	 */
	private $authSession;


    public function init()
    {
		$this->view->headLink()->appendStylesheet('/css/layout.css');

        $this->doctrine = Zend_Registry::get('doctrine');
        $this->em = $this->doctrine->getEntityManager();
        
        $this->campRepo  = $this->em->getRepository('\Entity\Camp');
        $this->loginRepo = $this->em->getRepository('\Entity\Login');

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
		$form = new Application_Form_Camp();
        $id = $this->getRequest()->getParam("id");

        if ($id == null)
        {
            throw new Exception('Id must be provided for the delete action');
        }
        
		$camp = $this->campRepo->find($id);

		$form->setData($camp);
		$form->setAction("/camp/save");
		$this->view->form = $form;
	}

    public function newAction()
    {
        $form = new Application_Form_Camp();
     	$form->setDefaults($this->getRequest()->getParams());

		$form->setAction("/camp/create");
		$this->view->form = $form;
    }


	public function saveAction()
	{
		$form = new Application_Form_Camp();

		if(!$form->isValid($this->getRequest()->getParams()))
		{
			$this->_forward("edit");
			return;
		}

		$id = $form->getId();

		if($id == "")
		{
			$camp = new Entity\Camp();
			$form->grabData($camp);

			$this->em->persist($camp);
		}
		else
		{
			$camp = $this->em->find("Entity\Camp", $id);
			$form->grabData($camp);
		}

		$this->em->flush();

		$this->_redirect("/camp/index");
	}

    public function createAction()
    {
        $form = new Application_Form_Camp();

		if(!$form->isValid($this->getRequest()->getParams()))
		{
			$this->_forward("new");
			return;
		}

		$camp = new Entity\Camp();
		$form->grabData($camp);

		$this->em->persist($camp);
		$this->em->flush();

		$this->_redirect("/camp/index");
    }


	public function committocampAction()
	{
		$login = $this->loginRepo->find($this->authSession->Login);
		$user = $login->getUser();
		$camp = $this->campRepo->find($this->getRequest()->getParam('id'));

		$service = new Service\UserService();
		$service->addUserToCamp($user, $camp);

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

