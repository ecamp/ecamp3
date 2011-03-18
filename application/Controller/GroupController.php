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
    }

    public function showAction()
    {
		$id = $this->getRequest()->getParam("group");
		$group = $this->em->getRepository("Entity\Group")->find($id);
		
		$this->view->group = $group;		
    }
	
	public function membersAction()
	{
		$id = $this->getRequest()->getParam("group");
		$group = $this->em->getRepository("Entity\Group")->find($id);
		
		$this->view->group = $group;
	}
	
	public function avatarAction()
	{
		$id = $this->getRequest()->getParam("group");
		
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

		$group = $this->em->getRepository("Entity\Group")->find($id);
		
		if( $group->getImageData() == null ) {
			$this->_redirect("img/default_group.png");
			
		} else {
			$this->getResponse()->setHeader("Content-type", $group->getImageMime());
			$this->getResponse()->setBody($group->getImageData());
		}
	}
	
	/** membership actions */
	
	public function requestAction(){
		$id = $this->getRequest()->getParam("group");
		$group = $this->em->getRepository("Entity\Group")->find($id);
		
		$this->me->sendMembershipRequestTo($group);
		
		$this->em->flush();
		$this->_helper->getHelper('Redirector')->gotoRoute(array('action'=>'show', 'group' => $id), 'group');
	}
	
	public function leaveAction(){
		$id = $this->getRequest()->getParam("group");
		$group = $this->em->getRepository("Entity\Group")->find($id);
		
		$this->me->deleteMembershipWith($group);
		
		$this->em->flush();
		$this->_helper->getHelper('Redirector')->gotoRoute(array('action'=>'show', 'group' => $id), 'group');
	}
	
	public function acceptAction(){

		$id = $this->getRequest()->getParam("id");
		$request = $this->em->getRepository("Entity\UserGroup")->find($id);
		
		$groupid = $this->getRequest()->getParam("group");
		$group = $this->em->getRepository("Entity\Group")->find($groupid);
		
		$group->acceptRequest($request, $this->me);
		
		$this->em->flush();
		$this->_helper->getHelper('Redirector')->gotoRoute(array('action'=>'show', 'group' => $groupid), 'group');
	}
	
	public function refuseAction(){
		$id = $this->getRequest()->getParam("id");
		$request = $this->em->getRepository("Entity\UserGroup")->find($id);
		
		$groupid = $this->getRequest()->getParam("group");
		$group = $this->em->getRepository("Entity\Group")->find($groupid);
		
		$group->refuseRequest($request, $this->me);
		
		$this->em->flush();
		$this->_helper->getHelper('Redirector')->gotoRoute(array('action'=>'show', 'group' => $groupid, 'user' => null), 'group');
	}

}