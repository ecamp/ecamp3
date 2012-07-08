<?php

namespace Core\Repository;

use Doctrine\ORM\EntityRepository;
use CoreApi\Entity\User;

class UserGroupRepository extends EntityRepository
{
	
	public function __construct($em, \Doctrine\ORM\Mapping\ClassMetadata $class)
	{
		parent::__construct($em, $class);
	}
	
	public function findMembershipsOfUser(User $user)
	{
		$query = $this->createQueryBuilder("ug")
					->where("ug.user = '" . $user->getId() . "'")
					->andWhere("ug.invitationAccepted = TRUE")
					->andWhere("ug.requestAcceptedBy_id is not null")
					->getQuery();
		
		return $query->getResult();
	}
	
	public function findMembershipsOfGroup(Group $group)
	{
		$query = $this->createQueryBuilder("ug")
					->where("ug.group = '" . $group->getId() . "'")
					->andWhere("ug.invitationAccepted = TRUE")
					->andWhere("ug.requestAcceptedBy_id is not null")
					->getQuery();
		
		return $query->getResult();
	}
	
	
	public function findMembershipRequests(\CoreApi\Entity\Group $group)
	{
		$query = $this->createQueryBuilder("ug")
					->where("ug.group = '" . $group->getId() . "'")
					->andWhere("ug.invitationAccepted = TRUE")
					->andWhere("ug.requestAcceptedBy_id is null")
					->getQuery();
		return $query->getResult();
	}
	
	public function findMembershipInvitations(\CoreApi\Entity\User $user)
	{
		$query = $this->createQueryBuilder("ug")
					->where("ug.group = '" . $group->getId() . "'")
					->andWhere("ug.invitationAccepted = FALSE")
					->andWhere("ug.requestAcceptedBy_id is not null")
					->getQuery();
		return $query->getResult();
	}
	
}
