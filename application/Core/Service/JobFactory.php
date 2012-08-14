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

namespace Core\Service;

use CoreApi\Entity\Job;

class JobFactory
{
	/**
	 * @var ServiceBase
	 */
	private $service;
	
	
	/**
	 * @var Doctrine\ORM\EntityManager
	 * @Inject Doctrine\ORM\EntityManager
	 */
	private $em;
	
	
	/**
	 * @var CoreApi\Acl\ContextProvider
	 * @Inject CoreApi\Acl\ContextProvider
	 */
	protected $contextProvider;
	
	
	public function __construct(ServiceBase $service)
	{
		$this->service = $service;
	}
	
	
	public function __call($method, $params)
	{
		$job = new Job();
		$job->setClass(get_class(($this->service)));
		$job->setMethod($method);
		$job->setContext($this->contextProvider->getContext());
		
		call_user_func_array(array($job, 'setParams'), $params);
		
		$this->em->persist($job);
	}
	
	
}