<?php

namespace Core\Repository;

use Doctrine\ORM\EntityRepository;
use CoreApi\Entity\User;
use CoreApi\Entity\Group;

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
					->andWhere("ug.requestAcceptedBy is not null")
					->getQuery();
		
		return $query->getResult();
	}
	
	public function findMembershipsOfGroup(Group $group)
	{
		$query = $this->createQueryBuilder("ug")
					->where("ug.group = '" . $group->getId() . "'")
					->andWhere("ug.invitationAccepted = TRUE")
					->andWhere("ug.requestAcceptedBy is not null")
					->getQuery();
		
		return $query->getResult();
	}
	
	
	public function findMembershipRequests(Group $group)
	{
		$query = $this->createQueryBuilder("ug")
					->where("ug.group = '" . $group->getId() . "'")
					->andWhere("ug.invitationAccepted = TRUE")
					->andWhere("ug.requestAcceptedBy is null")
					->getQuery();
		return $query->getResult();
	}
	
	public function findMembershipInvitations(User $user)
	{
		$query = $this->createQueryBuilder("ug")
					->where("ug.user = '" . $user->getId() . "'")
					->andWhere("ug.invitationAccepted = FALSE")
					->andWhere("ug.requestAcceptedBy is not null")
					->getQuery();
		return $query->getResult();
	}
	
}
