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

namespace WebApp\QuickSwitch;

class Manager
{
	
	/**
	 * @var CoreApi\Acl\ContextProvider
	 * @Inject CoreApi\Acl\ContextProvider
	 */
	protected $contextProvider;
	
	/**
	 * @var WebApp\QuickSwitch\Providers\CampLinkProvider
	 * @Inject WebApp\QuickSwitch\Providers\CampLinkProvider
	 */
	private $campLinkProvider;
	
	
	private $links = null;
	
	
	public function getLinks()
	{
		if(is_null($this->links))
		{	
			$this->links = array();
			
			if($this->contextProvider->getContext()->getMe() != null)
			{
				$this->links = array_merge($this->links, $this->campLinkProvider->getLinks());
			}
		}
		return $this->links;
	}
	
	
	public function getLink($id)
	{
		if(is_null($this->links))
		{	$this->getLinks();	}
		
		return array_key_exists($id, $this->links) ? $this->links[$id] : false;
	}
	
	
	public function getControllerUrl()
	{
		$params = array(
			'controller' 	=> 'quick',
			'action' 		=> 'forward'
		);
			
		return \Zend_Controller_Front::getInstance()->getRouter()->assemble($params, 'web+general');
	}
	
	
	public function getForwardKeyword()
	{
		return "to";
	}
}