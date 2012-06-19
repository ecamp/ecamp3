<?php

namespace Core\Repository;

use Doctrine\ORM\EntityRepository;


class UserRelationshipRepository extends EntityRepository
{
	
	public function __construct($em, \Doctrine\ORM\Mapping\ClassMetadata $class)
	{
		parent::__construct($em, $class);
	}
	
	public function findByUsers(\CoreApi\Entity\User $user1, \CoreApi\Entity\User $user2)
	{
		$idList = "(" . $user1->getId() . ", " . $user2->getId() . ")";
	
		$query = $this->createQueryBuilder("ur")
					->where("ur.from_id IN " . $idList)
					->andWhere("ur.to_id IN " . $idList)
					->getQuery();
		return $query->getResult();
	}
	
	
	public function findRequests(\CoreApi\Entity\User $user)
	{
		$query = $this->createQueryBuilder("ur")
					->where("ur.from_id IN " . $idList)
					->andWhere("ur.to_id IN " . $idList)
					->getQuery();
		return $query->getResult();
	}
}
