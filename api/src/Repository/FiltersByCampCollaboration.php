<?php

namespace App\Repository;

use App\Entity\Camp;
use App\Entity\User;
use App\Entity\UserCamp;
use Doctrine\ORM\QueryBuilder;

trait FiltersByCampCollaboration {
    /**
     * Applies a filter that checks for an active campCollaboration of the passed user, or that
     * the camp is a prototype.
     * Assumes the queryBuilder already knows how to get to the corresponding camp. You can pass
     * the alias of the camp as the third argument if it's anything other than "camp".
     */
    public function filterByCampCollaboration(QueryBuilder $queryBuilder, User $user, string $campAlias = 'camp'): void {
        $campsQry = $queryBuilder->getEntityManager()->createQueryBuilder();
        $campsQry->select('identity(uc.camp)');
        $campsQry->from(UserCamp::class, 'uc');
        $campsQry->where('uc.user = :current_user');

        $queryBuilder->andWhere($queryBuilder->expr()->in($campAlias, $campsQry->getDQL()));
        $queryBuilder->setParameter('current_user', $user);
    }
}
