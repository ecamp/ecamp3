<?php

namespace eCamp\Core\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\CampCollaboration;
use eCamp\Core\Entity\User;

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

    public function findByUserAndCamp(User $user, Camp $camp): ?CampCollaboration {
        $q = $this->createQueryBuilder('c');
        $q->where('c.user = :user')
            ->andWhere('c.camp = :camp')
        ;
        $q->setParameter('user', $user);
        $q->setParameter('camp', $camp);

        try {
            $q->setMaxResults(1);

            return $q->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            // This shouldn't ever happen
            return null;
        }
    }

    public function saveWithoutAcl(CampCollaboration $campCollaboration): void {
        $this->getEntityManager()->persist($campCollaboration);
        $this->getEntityManager()->flush();
    }
}
