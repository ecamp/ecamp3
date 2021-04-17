<?php

namespace eCamp\Core\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use eCamp\Core\Entity\ContentNode;

class ContentNodeRepository extends EntityRepository {
    public function getHighestChildPosition(ContentNode $parent, string $slot): int {
        $q = $this->createQueryBuilder('cn');
        $q->select('max(cn.position)');
        $q->andWhere('cn.parent = :parent');
        $q->andWhere('cn.slot = :slot');
        $q->setParameters(['parent' => $parent->getId(), 'slot' => $slot]);

        try {
            return $q->getQuery()->getSingleScalarResult() ?? 0;
        } catch (NoResultException | NonUniqueResultException $e) {
            // This shouldn't ever happen
            return 0;
        }
    }
}
