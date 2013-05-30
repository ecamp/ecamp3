<?php

namespace EcampCore\Repository;

use Doctrine\ORM\EntityRepository;

use EcampCore\Entity\UserCamp;
use EcampCore\ServiceManager\AutoInjectDependenciesInterface;

class CampRepository 
	extends EntityRepository
{
	
	public function findUserCamps($userId){
		
		$q = $this->_em->createQuery(
			"	SELECT 	c" . 
			"	FROM 	EcampCore\Entity\Camp c" . 
			"	WHERE 	c.owner = :userId" .
			"	OR		EXISTS (" . 
			"		SELECT 	1" . 
			"		FROM	EcampCore\Entity\UserCamp uc" . 
			"		WHERE	uc.camp = c.id" . 
			"		AND		uc.user = :userId" .
			"		AND		uc.role > :role" .
			"	)"
		);
		
		
		$q->setParameter('userId', 	$userId);
		$q->setParameter('role', 	UserCamp::ROLE_NONE);
		
		//die($q->getSQL());
		
		return $q->getResult();
	}
	
	public function findPersonalCamps($userId){
		return $this->findBy(array('owner' => $userId));
	}
	
	public function findPersonalCamp($userId, $campName){
		return $this->findOneBy(array('owner' => $userId, 'name' => $campName));
	}
	
	
	public function findGroupCamps($groupId){
		return $this->findBy(array('group' => $groupId));
	}

	public function findGroupCamp($groupId, $campName){
		return $this->findOneBy(array('group' => $groupId, 'name' => $campName));
	}
}
