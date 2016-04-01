<?php

namespace EcampCore\Repository;

use EcampCore\Entity\CampOwnerInterface;
use EcampCore\Entity\GroupMembership;

/**
 * Class CampOwnerRepository
 * @package EcampCore\Repository
 *
 * @method CampOwnerInterface find($id)
 */
class CampOwnerRepository extends BaseRepository
{
    public function findPossibleCampOwner()
    {
        $q = $this->_em->createQuery("
            SELECT  o
            FROM    EcampCore\Entity\AbstractCampOwner o
            WHERE   o.id = :userId
            OR      EXISTS(
                SELECT  1
                FROM    EcampCore\Entity\GroupMembership gm
                WHERE   gm.group = o.id
                AND     gm.user = :userId
                AND     gm.status = :status
            )
        ");
        $q->setParameter('userId', $this->getAuthenticatedUserId());
        $q->setParameter('status', GroupMembership::STATUS_ESTABLISHED);

        return $q->getResult();
    }

}
