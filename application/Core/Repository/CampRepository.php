<?php

namespace Core\Repository;

use Doctrine\ORM\EntityRepository;

use CoreApi\Entity\User;
use CoreApi\Entity\Group;


class CampRepository extends EntityRepository
{
	
	public function findUserCamp(User $user, $campName)
	{
		return $this->findOneBy(array(
			'owner' => $user,
			'name' => $campName
		));
	}
	
	
	public function findGroupCamp(Group $group, $campName)
	{
		return $this->findOneBy(array(
			'group' => $group,
			'name' => $campName
		));
		
	}
	
	
}
