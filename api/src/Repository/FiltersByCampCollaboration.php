<?php

namespace App\Repository;

use App\Entity\Camp;
use App\Entity\CampCollaboration;
use App\Entity\User;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;

trait FiltersByCampCollaboration {
    /**
     * Applies a filter that checks for an active campCollaboration of the passed user, or that
     * the camp is a prototype.
     * Assumes the queryBuilder already knows how to get to the corresponding camp. You can pass
     * the alias of the camp as the third argument if it's anything other than "camp".
     */
    public function filterByCampCollaboration(QueryBuilder $queryBuilder, User $user, string $campRef): void {
        $em = $queryBuilder->getEntityManager();

        $campQry = $em->createQueryBuilder();
        $campQry
            ->from(Camp::class, 'c')
            ->select('c')
            ->leftJoin(
                'c.collaborations',
                'cc',
                Join::WITH,
                $campQry->expr()->andX(
                    $campQry->expr()->eq('cc.user', ':current_user'),
                    $campQry->expr()->eq('cc.status', "'".CampCollaboration::STATUS_ESTABLISHED."'")
                )
            )
            ->where(
                $campQry->expr()->orX(
                    $campQry->expr()->eq('c.isPrototype', 'TRUE'),
                    $campQry->expr()->isNotNull('cc.id')
                )
            )
        ;

        $queryBuilder->andWhere($queryBuilder->expr()->in($campRef, $campQry->getDQL()));
        $queryBuilder->setParameter('current_user', $user);
    }
}
