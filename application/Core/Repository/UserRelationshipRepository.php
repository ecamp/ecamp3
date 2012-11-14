<?php

namespace Core\Repository;


use Doctrine\ORM\EntityRepository;
use CoreApi\Entity\User;
use CoreApi\Entity\UserRelationship;

class UserRelationshipRepository extends EntityRepository
{
	
	public function __construct($em, \Doctrine\ORM\Mapping\ClassMetadata $class)
	{
		parent::__construct($em, $class);
	}
	
	/**
	 * @param CoreApi\Entity\User $user1
	 * @param CoreApi\Entity\User $user2
	 * @return CoreApi\Entity\UserRelationship
	 */
	public function findByUsers(User $fromUser, User $toUser)
	{
		$query = $this->createQueryBuilder("ur")
					->where("ur.from = '" . $fromUser->getId() . "'")
					->andWhere("ur.to = '" . $toUser->getId() . "'")
					->andWhere("ur.type = " . UserRelationship::TYPE_FRIEND)
					->getQuery();
		$ur = $query->getResult();
		$ur = $ur ? $ur[0] : null;
		return $ur;
	}
	
	
	/**
	 * @param CoreApi\Entity\User $user
	 */
	public function findFriends(User $user)
	{
		$query = $this->createQueryBuilder("ur")
					->where("ur.type = " . UserRelationship::TYPE_FRIEND)
					->andWhere("ur.from = '" . $user->getId() . "'")
					->andWhere("ur.counterpart is not null")
					->getQuery();
		return $query->getResult();
	}
	
	
	/**
	 * @param CoreApi\Entity\User $user
	 */
	public function findRequests(User $user)
	{
		$query = $this->createQueryBuilder("ur")
					->where("ur.from = '" . $user->getId() . "'")
					->andWhere("ur.counterpart IS NULL")
					->andWhere("ur.type = " . UserRelationship::TYPE_FRIEND)
					->getQuery();
		return $query->getResult();
	}
	
	
	/**
	 * @param CoreApi\Entity\User $user
	 */
	public function findInvitation(User $user)
	{
		$query = $this->createQueryBuilder("ur")
					->where("ur.to = '" . $user->getId() . "'")
					->andWhere("ur.counterpart IS NULL")
					->andWhere("ur.type = " . UserRelationship::TYPE_FRIEND)
					->getQuery();
		return $query->getResult();
	}
	
}
