<?php

namespace Core\Repository;

use Doctrine\ORM\EntityRepository;


class UserRepository extends EntityRepository
{
	
	public function __construct($em, \Doctrine\ORM\Mapping\ClassMetadata $class)
	{
		parent::__construct($em, $class);
	}
	
	public function findFriendsOf(\CoreApi\Entity\User $user)
	{
		$query = $this->createQueryBuilder("u")
				->innerJoin("u.relationshipFrom","rel_to")
				->innerJoin("rel_to.to", "friend")
				->innerJoin("friend.relationshipFrom", "rel_back")
				->where("rel_to.type = " . \CoreApi\Entity\UserRelationship::TYPE_FRIEND)
				->andwhere("rel_back.type = " . \CoreApi\Entity\UserRelationship::TYPE_FRIEND)
				->andwhere("rel_back.to = u.id")
				->andwhere("friend.id = " . $user->getId())
				->getQuery();

	    return $query->getResult();
	}
	
	public function findFriendshipInvitationsOf(\CoreApi\Entity\User $user)
	{
		$query = $this->createQueryBuilder("u")
				->innerJoin("u.relationshipFrom","rel_to")
				->innerJoin("rel_to.to", "friend")
				->leftJoin("friend.relationshipFrom", "rel_back", \Doctrine\ORM\Query\Expr\Join::WITH, 'rel_back.to = rel_to.from' )
				->where("rel_to.type = " . (\CoreApi\Entity\UserRelationship::TYPE_FRIEND))
				->andwhere("rel_back.to IS NULL")
				->andwhere("friend.id = " . $user->getId())
				->getQuery();
				
	    return $query->getResult();
	}
	
	
	
}
