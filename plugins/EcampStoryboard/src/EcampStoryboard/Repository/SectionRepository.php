<?php

namespace EcampStoryboard\Repository;

use EcampCore\Entity\PluginInstance;

use EcampStoryboard\Entity\Section;

use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityRepository;

class SectionRepository extends EntityRepository
{
	
	/**
	 * @param Section $section
	 * @return Section
	 */
	public function findNextSection(Section $section){
		$q = $this->getEntityManager()->createQuery("
			SELECT 		s 
			FROM 		EcampStoryboard\Entity\Section s 
			WHERE 		s.pluginInstance = :pluginInstance
			AND			s.position > :position
			ORDER BY	s.position ASC");
		
		$q->setParameter('pluginInstance', $section->getPluginInstance()->getId());
		$q->setParameter('position', $section->getPosition());
		
		$q->setMaxResults(1);
		return $q->getOneOrNullResult();
	}
	
	
	/**
	 * @param Section $section
	 * @return Section
	 */
	public function findPrevSection(Section $section){
		$q = $this->getEntityManager()->createQuery("
			SELECT 		s
			FROM 		EcampStoryboard\Entity\Section s
			WHERE 		s.pluginInstance = :pluginInstance
			AND			s.position < :position
			ORDER BY	s.position DESC");
		
		$q->setParameter('pluginInstance', $section->getPluginInstance()->getId());
		$q->setParameter('position', $section->getPosition());
		
		$q->setMaxResults(1);
		return $q->getOneOrNullResult();
	}
	
	
	public function getMaxPosition(PluginInstance $pluginInstance){
		$q = $this->getEntityManager()->createQuery("
			SELECT		max(s.position)
			FROM		EcampStoryboard\Entity\Section s
			WHERE		s.pluginInstance = :pluginInstance");
		
		$q->setParameter('pluginInstance', $pluginInstance->getId());
		
		return $q->getSingleScalarResult();
	}
	
}
