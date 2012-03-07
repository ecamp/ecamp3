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

class WebApp_AvatarController extends \Zend_Controller_Action
{
	
	/**
	 * @var \CoreApi\Service\Avatar
	 * @Inject \CoreApi\Service\Avatar
	 */
	protected $avatarService;
	
	
	public function init()
	{
		\Zend_Registry::get('kernel')->InjectDependencies($this);
	}

	public function userAction()
	{
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

		$id = $this->getRequest()->getParam("id");
		
		$this->avatarService->sendUserAvatar($id);
	}

	public function groupAction()
	{
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

		$id = $this->getRequest()->getParam("id");
	    
		$this->avatarService->sendGroupAvatar($id);
	}
	
}