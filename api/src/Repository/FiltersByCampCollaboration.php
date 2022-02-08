<?php

namespace App\Repository;

use App\Entity\CampCollaboration;
use App\Entity\User;
use Doctrine\ORM\QueryBuilder;

trait FiltersByCampCollaboration {
    /**
     * Applies a filter that checks for an active campCollaboration of the passed user, or that
     * the camp is a prototype.
     * Assumes the queryBuilder already knows how to get to the corresponding camp. You can pass
     * the alias of the camp as the third argument if it's anything other than "camp".
     */
    public function filterByCampCollaboration(QueryBuilder $queryBuilder, User $user, string $campAlias = 'camp'): void {
        $queryBuilder->leftJoin("{$campAlias}.collaborations", "filter_{$campAlias}_campCollaboration");
        $queryBuilder->andWhere(
            $queryBuilder->expr()->orX(
                // user is established collaborator in the camp
                $queryBuilder->expr()->andX(
                    $queryBuilder->expr()->eq("filter_{$campAlias}_campCollaboration.user", ':current_user'),
                    $queryBuilder->expr()->eq("filter_{$campAlias}_campCollaboration.status", ':established'),
                ),

                // camp is a Prototype = all Prototypes are readable
                $queryBuilder->expr()->eq("{$campAlias}.isPrototype", ':true'),
            )
        );
        $queryBuilder->setParameter('current_user', $user);
        $queryBuilder->setParameter('established', CampCollaboration::STATUS_ESTABLISHED);
        $queryBuilder->setParameter('true', true);
    }
}
