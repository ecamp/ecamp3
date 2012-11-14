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

use CoreApi\Entity\UId;
use CoreApi\Entity\BaseEntity;

use Doctrine\ORM\Event\LifecycleEventArgs;


class IdGenerator
{
	const DELETE_UID = 'DELETE CoreApi\Entity\Uid s WHERE s.id = :id';
	
	
	/**
	 * @var Doctrine\ORM\EntityManager
	 * @Inject Doctrine\ORM\EntityManager
	 */
	private $em;
	
	
	public function prePersist(LifecycleEventArgs $eventArgs)
	{
		$entity = $eventArgs->getEntity();
		
		if($entity instanceof BaseEntity)
		{
			$class = get_class($entity);
			$uid = $this->getUid($class);
			
			EntityIdSetter::SetId($entity, $uid->getId());
		}
	}
	
	public function preRemove(LifecycleEventArgs $eventArgs)
	{
		$entity = $eventArgs->getEntity();
	
		if($entity instanceof BaseEntity)
		{
			$q = $this->em->createQuery(self::DELETE_UID);
			$q->execute(array('id' => $entity->getId()));
		}
	}
	
	public function getUid($class)
	{
		$uid = new UId($class);
		$this->em->persist($uid);
	
		return $uid;
	}
	
}


class EntityIdSetter
	extends BaseEntity
{
	public static function SetId(BaseEntity $entity, $id)
	{
		$entity->id = $id;
	}
}	