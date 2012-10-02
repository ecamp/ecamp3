<?php

namespace Core\Repository;

use CoreApi\Entity\User;
use CoreApi\Entity\UserRelationship;

use Doctrine\ORM\EntityRepository;


class UserRepository extends EntityRepository
{
	
	public function __construct($em, \Doctrine\ORM\Mapping\ClassMetadata $class)
	{
		parent::__construct($em, $class);
	}
	
	public function findFriends(User $user){
		$query = $this->createQueryBuilder("u")
				->innerJoin("u.relationshipFrom", "ur")
				->where("ur.to = '" . $user->getId() . "'")
				->andWhere("ur.counterpart is not null")
				->andWhere("ur.type = " . UserRelationship::TYPE_FRIEND)
				->getQuery();
		
		return $query->getResult();
	}
	
	public function findFriendInvitations(User $user){
		$query = $this->createQueryBuilder("u")
				->innerJoin("u.relationshipFrom", "ur")
				->where("ur.to = '" . $user->getId() . "'")
				->andWhere("ur.counterpart is null")
				->andWhere("ur.type = " . UserRelationship::TYPE_FRIEND)
				->getQuery();
		
		return $query->getResult();
	}
	
	public function findFriendRequests(User $user){
		$query = $this->createQueryBuilder("u")
				->innerJoin("u.relationshipTo", "ur")
				->where("ur.from = '" . $user->getId() . "'")
				->andWhere("ur.counterpart is null")
				->andWhere("ur.type = " . UserRelationship::TYPE_FRIEND)
				->getQuery();
		
		return $query->getResult();
	}
	
	
}
