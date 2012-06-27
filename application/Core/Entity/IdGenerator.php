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

namespace Core\Entity;

use CoreApi\Entity\Seqnr;
use CoreApi\Entity\BaseEntity;

use Doctrine\ORM\Event\LifecycleEventArgs;


class IdGenerator
{
	
	/**
	 * @var Doctrine\ORM\EntityManager
	 * @Inject Doctrine\ORM\EntityManager
	 */
	private $em;
	
	
	private $reflectors = array();
	
	
	public function prePersist(LifecycleEventArgs $eventArgs)
	{
		$entity = $eventArgs->getEntity();
		
		if($entity instanceof BaseEntity)
		{
			$class = get_class($entity);
			
			$seqnr = new Seqnr($class);
			$this->em->persist($seqnr);
			$this->em->flush($seqnr);
			
			$r = $this->getPropertyReflector($class);
			$r->setValue($entity, $seqnr->getId());
			
		}
	}
	
	
	public function getPropertyReflector($class)
	{
		if(!array_key_exists($class, $this->reflectors))
		{
			$reflector = new \Zend_Reflection_Property($class, 'id');
			$reflector->setAccessible(true);
			
			$this->reflectors[$class] = $reflector;
		}
		
		return $this->reflectors[$class];
	}
	
}