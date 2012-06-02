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

namespace WebApp\QuickSwitch\Providers;

use WebApp\QuickSwitch\ILinkProvider,
	WebApp\QuickSwitch\Link,
	Zend_Controller_Front;

class CampLinkProvider implements ILinkProvider
{
	
	/**
	 * @var CoreApi\Acl\ContextProvider
	 * @Inject CoreApi\Acl\ContextProvider
	 */
	protected $contextProvider;
	
	
	private $links = null;
	
	
	public function getProviderName()
	{
		return "CAMP";
	}
	
	
	public function getLinks()
	{
		if(is_array($this->links))
		{	return $this->links;	}
		
		$this->links = array();
		$me = $this->contextProvider->getContext()->getMe();
		$front = Zend_Controller_Front::getInstance();
		
		foreach($me->getAcceptedUserCamps() as $usercamp)
		{
			$camp = $usercamp->getCamp();
			$id = $this->getProviderName() . $camp->getId();
			
			$params = array(
				'controller' 	=> 'camp',
				'action' 		=> 'show',
				'camp'			=> $camp->getId()
			);
			
			if($camp->belongsToUser())
			{
				$params['user'] = $camp->getOwner()->getId();
				$url = $front->getRouter()->assemble($params, 'web+user+camp');
			}
			else
			{
				$params['group'] = $camp->getGroup()->getId();
				$url = $front->getRouter()->assemble($params, 'web+group+camp');
			}
			
			$this->links[$id] = new Link($id, $url, $camp);
		}
		
		return $this->links;
	}
}