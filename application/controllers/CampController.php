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
	 * @var Zend_Session_Namespace
	 */
	private $authSession;


    public function init()
    {
		$this->view->headLink()->appendStylesheet('/css/layout.css');

		/** @var \Bisna\Application\Container\DoctrineController $doctrineContainer  */
		$doctrineContainer = Zend_Registry::getInstance()->get("doctrine");

		$this->em = $doctrineContainer->getEntityManager();

		
		$this->authSession = new Zend_Session_Namespace('Zend_Auth');
	}


    public function indexAction()
    {
		if(is_null($this->authSession->Login))
		{
			$this->_forward("index", "login");
			return;
		}

		$loginId = $this->authSession->Login->GetId();

		/** @var $login eCamp\Entity\Login */
		$login = $this->em->find("eCamp\Entity\Login", $loginId);

		$myUserCamps = new ArrayObject();

		foreach($login->GetUser()->GetCamps() as $userToCamp)
		{	$myUserCamps->append(new eCamp\PMod\UserToCampPMod($userToCamp));	}

		$this->view->myUserCamps = $myUserCamps;



		$allCamps = new ArrayObject();
		foreach($this->em->getRepository("eCamp\Entity\Camp")->findAll() as $camp)
		{	$allCamps->append(new eCamp\PMod\CampPMod($camp));	}

		$this->view->allCamps = $allCamps;
    }


	public function editcampAction()
	{

		$campForm = new Application_Form_CampForm();

		if($this->getRequest()->getParam("Id") != "")
		{
			$campId = $this->getRequest()->getParam("Id");
			$camp = $this->em->find("\eCamp\Entity\Camp", $campId);

			$campForm->setData($camp);
		}
		else
		{
			$campForm->setDefaults($this->getRequest()->getParams());
		}
		
		$campForm->setAction("/camp/savecamp");

		$this->view->campForm = $campForm;
	}


	public function savecampAction()
	{
		
		$campForm = new Application_Form_CampForm();

		if(!$campForm->isValid($this->getRequest()->getParams()))
		{
			$this->_forward("editcamp");
			return;
		}


		$campId = $campForm->getId();

		if($campId == "")
		{
			$camp = new eCamp\Entity\Camp();
			$campForm->grabData($camp);

			$this->em->persist($camp);
		}
		else
		{
			$camp = $this->em->find("eCamp\Entity\Camp", $campId);
			$campForm->grabData($camp);
		}

		$this->em->flush();

		$this->_redirect("/camp/index");
	}


	public function committocampAction()
	{
		$loginId = $this->authSession->Login->GetId();

		/** @var $login eCamp\Entity\Login */
		$login = $this->em->find("eCamp\Entity\Login", $loginId);

		/** @var $user eCamp\Entity\User */
		$user = $login->GetUser();


		$campId = $this->getRequest()->getParam('Id');

		/** @var $camp eCamp\Entity\Camp */
		$camp = $this->em->find("eCamp\Entity\Camp", $campId);


		$userCamp = new eCamp\Entity\UserToCamp();
		$userCamp->setUser($user);
		$userCamp->setCamp($camp);


		$this->em->persist($userCamp);
		$this->em->flush();

		$this->_redirect("/camp");
	}


	public function cancelfromcampAction()
	{

		$userCampId = $this->getRequest()->getParam('Id');

		$userToCamp = $this->em->find("eCamp\Entity\UserToCamp", $userCampId);

		if($userToCamp != null)
		{
			$this->em->remove($userToCamp);
			$this->em->flush();
		}


		$this->_redirect("/camp");
	}
}

