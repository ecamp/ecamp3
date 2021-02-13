<?php

namespace eCamp\Core\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use eCamp\Core\Entity\CampCollaboration;

class CampCollaborationRepository extends EntityRepository {
    public function findByInviteKey(string $inviteKey): ?CampCollaboration {
        $q = $this->createQueryBuilder('c');
        $q->where('c.inviteKey = :inviteKey');
        $q->setParameter('inviteKey', $inviteKey);

        try {
            $q->setMaxResults(1);

            return $q->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            // This shouldn't ever happen
            return null;
        }
    }

    public function saveWithoutAcl(CampCollaboration $campCollaboration) {
        $this->getEntityManager()->persist($campCollaboration);
        $this->getEntityManager()->flush();
    }
}
