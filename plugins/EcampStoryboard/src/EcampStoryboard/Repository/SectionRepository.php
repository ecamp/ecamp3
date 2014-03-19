<?php

namespace EcampStoryboard\Repository;

use EcampCore\Entity\EventPlugin;
use EcampStoryboard\Entity\Section;
use Doctrine\ORM\EntityRepository;

class SectionRepository extends EntityRepository
{

    /**
     * @param  Section $section
     * @return Section
     */
    public function findNextSection(Section $section)
    {
        $q = $this->getEntityManager()->createQuery("
            SELECT 		s
            FROM 		EcampStoryboard\Entity\Section s
            WHERE 		s.eventPlugin = :eventPlugin
            AND			s.position > :position
            ORDER BY	s.position ASC");

        $q->setParameter('eventPlugin', $section->getEventPlugin()->getId());
        $q->setParameter('position', $section->getPosition());

        $q->setMaxResults(1);

        return $q->getOneOrNullResult();
    }

    /**
     * @param  Section $section
     * @return Section
     */
    public function findPrevSection(Section $section)
    {
        $q = $this->getEntityManager()->createQuery("
            SELECT 		s
            FROM 		EcampStoryboard\Entity\Section s
            WHERE 		s.eventPlugin = :eventPlugin
            AND			s.position < :position
            ORDER BY	s.position DESC");

        $q->setParameter('eventPlugin', $section->getEventPlugin()->getId());
        $q->setParameter('position', $section->getPosition());

        $q->setMaxResults(1);

        return $q->getOneOrNullResult();
    }

    public function getMaxPosition(EventPlugin $eventPlugin)
    {
        $q = $this->getEntityManager()->createQuery("
            SELECT		max(s.position)
            FROM		EcampStoryboard\Entity\Section s
            WHERE		s.eventPlugin = :eventPlugin");

        $q->setParameter('eventPlugin', $eventPlugin->getId());

        return $q->getSingleScalarResult();
    }

}
