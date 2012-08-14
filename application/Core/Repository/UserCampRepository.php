<?php

namespace Core\Repository;

use Doctrine\ORM\EntityRepository;


class UserCampRepository extends EntityRepository
{
	
	public function __construct($em, \Doctrine\ORM\Mapping\ClassMetadata $class)
	{
		parent::__construct($em, $class);
	}
	
	public function findCollaboratorsByCamp(\CoreApi\Entity\Camp $camp)
	{
		$query = $this->createQueryBuilder("uc")
					->where("us.camp_id = '" . $camp->getId() . "'")
					->andwhere("invitationAccepted > 0")
					->andwhere("requestAcceptedBy_id is not null")
					->getQuery();
		return $query->getResult();
	}
	
	public function findCollaboratorsByUser(\CoreApi\Entity\User $user)
	{
		$query = $this->createQueryBuilder("uc")
					->where("us.user_id = '" . $user->getId() . "'")
					->andwhere("invitationAccepted > 0")
					->andwhere("requestAcceptedBy_id is not null")
					->getQuery();
		return $query->getResult();
	}
	
	public function findOpenRequestsByCamp(\CoreApi\Entity\Camp $camp)
	{
		$query = $this->createQueryBuilder("uc")
					->where("us.camp_id = '" . $camp->getId() . "'")
					->andwhere("invitationAccepted > 0")
					->andwhere("requestAcceptedBy_id is null")
					->getQuery();
		return $query->getResult();
	}
	
	public function findOpenInvitationsByUser(\CoreApi\Entity\User $user)
	{
	$query = $this->createQueryBuilder("uc")
					->where("us.user_id = '" . $user->getId() . "'")
					->andwhere("invitationAccepted = 0")
					->andwhere("requestAcceptedBy_id is not null")
					->getQuery();
	return $query->getResult();
	}
	
}
