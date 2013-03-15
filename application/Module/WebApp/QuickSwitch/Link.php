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


class Link
{
	
	public function __construct($id, $url, $entity)
	{
		$this->id = $id;
		$this->url = $url;
		$this->entity = $entity;
		$this->type = get_class($entity);
	}
	
	public $id;
	
	public $url;
	
	public $entity;
	
	public $type;
	
	public function getQuickUrl()
	{
		$params = array(
			'controller' 	=> 'quick',
			'action' 		=> 'forward',
			'to'			=> $this->id
		);
			
		return \Zend_Controller_Front::getInstance()->getRouter()->assemble($params, 'web+general');
	}

}