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
 
namespace ApiApp\Controller;

class BaseController extends \Zend_Controller_Action
{
	/**
	 * @var PhpDI\IKernel
	 * @Inject PhpDI\IKernel
	 */
	protected $kernel;
	
	/**
	 * @var Doctrine\ORM\EntityManager
	 * @Inject Doctrine\ORM\EntityManager
	 */
	protected $em;
	
	/**
	 * @var CoreApi\Acl\ContextProvider
	 * @Inject CoreApi\Acl\ContextProvider
	 */
	protected $contextProvider;
	
	/**
	 * logged in user
	 * @var CoreApi\Entity\User
	 */
	protected $me;
	
	public function init()
	{
		parent::init();
		
		\Zend_Registry::get('kernel')->Inject($this);
		
		$this->me = $this->contextProvider->getContext()->getMe();
		
		$this->getResponse()->setHeader('Content-Type', 'application/json');
		$this->getResponse()->setHeader('Access-Control-Allow-Origin', 'http://www.ecamp3.dev');
		$this->getResponse()->setHeader('Access-Control-Allow-Credentials', 'true');
		
	}
}
