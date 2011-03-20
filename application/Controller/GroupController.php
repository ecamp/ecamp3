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


class GroupController extends \Controller\BaseController
{
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
	    $this->group = $this->em->getRepository("Entity\Group")->find($groupid);
	    $this->view->group = $this->group;

	    
	    $pages = array(
			array(
			'label'      => 'Overview',
			'title'      => 'Overview',
			'controller' => 'group',
			'action'     => 'show'),

		    array(
			'label'      => 'Camps / Courses',
			'title'      => 'Camps / Courses',
			'controller' => 'group',
			'action'     => 'camps'),

		    array(
			'label'      => 'Members',
			'title'      => 'Members',
			'controller' => 'group',
			'action'     => 'members')
	    );

	    $container = new Zend_Navigation($pages);
		$this->view->getHelper('navigation')->setContainer($container);

	    /* inject group id into navigation */
	    foreach($container->getPages() as $page){
			$page->setParams(array(
				'group' => $this->group->getId()
			));
		}
    }

    public function showAction()
    {
    }
	
	public function membersAction()
	{
	}

	public function campsAction(){
	}

	public function deletecampAction(){
		$id = $this->getRequest()->getParam("id");
	    $camp = $this->em->getRepository("Entity\Camp")->find($id);
		
	    $this->em->remove($camp);
		$this->em->flush();

		$this->_helper->getHelper('Redirector')->gotoRoute(array('action'=>'camps', 'group' => $this->group->getId()), 'group');
	}

	public function newcampAction(){
		$form = new \Form\Camp();
		
		$form->setDefaults($this->getRequest()->getParams());

		$this->view->form = $form;
	}

	public function createcampAction(){
		$form = new \Form\Camp();
		
		if(!$form->isValid($this->getRequest()->getParams()))
		{
			$this->view->form = $form;
			$this->render("newcamp");
			return;
		}

		$this->em->getConnection()->beginTransaction();
		try {
			$camp = new Entity\Camp();
			$period = new Entity\Period($camp);

			$camp->setGroup($this->group);
			$camp->setCreator($this->me);

			$form->grabData($camp, $period);

			$this->em->persist($camp);
			$this->em->persist($period);

			$this->em->flush();
			$this->em->getConnection()->commit();
		} catch (Exception $e) {
			$this->em->getConnection()->rollback();
			$this->em->close();

			$form->getElement("name")->addError("Name has already been taken.");
			$this->view->form = $form;
			$this->render("newcamp");
			return;
		}

		$this->_helper->getHelper('Redirector')->gotoRoute(array('action'=>'camps', 'group' => $this->group->getId()), 'group');
	}

	
	/** membership actions */
	
	public function requestAction(){

		$this->me->sendMembershipRequestTo($this->group);
		
		$this->em->flush();
		$this->_helper->flashMessenger->addMessage(array('info' => $this->t->translate("Your request has been sent to the group managers.")));
		$this->_helper->getHelper('Redirector')->gotoRoute(array('action'=>'show', 'group' => $this->group->getId()), 'group');
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

	public function kickoutAction(){
		$userid = $this->getRequest()->getParam("user");
		$user   = $this->em->getRepository("Entity\User")->find($userid);

		if( $this->me->isManagerOf($this->group) )
			$user->deleteMembershipWith($this->group);

		$this->em->flush();
		$this->_helper->flashMessenger->addMessage(array('success' => $this->t->translate('%1$s is not a member of %2$s anymore.', $user->getUsername(), $this->group->getName())));
		$this->_helper->getHelper('Redirector')->gotoRoute(array('action'=>'members', 'group' => $this->group->getId(), 'user'=>null ), 'group');
	}
	
	public function acceptAction(){

		$id = $this->getRequest()->getParam("id");
		$request = $this->em->getRepository("Entity\UserGroup")->find($id);
		
		$this->group->acceptRequest($request, $this->me);
		
		$this->em->flush();
		$this->_helper->flashMessenger->addMessage(array('success' => $this->t->translate('%1$s is now a member of %2$s.', $request->getUser()->getUsername(), $request->getGroup()->getName())));
		$this->_helper->getHelper('Redirector')->gotoRoute(array(), 'general');
	}
	
	public function refuseAction(){
		$id = $this->getRequest()->getParam("id");
		$request = $this->em->getRepository("Entity\UserGroup")->find($id);
		
		$this->group->refuseRequest($request, $this->me);
		
		$this->em->flush();
		$this->_helper->flashMessenger->addMessage(array('info' => $this->t->translate('The membership request has been refused.')));
		$this->_helper->getHelper('Redirector')->gotoRoute(array(), 'general');
	}

}