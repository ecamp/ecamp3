<?php

namespace App\Repository;

use App\Doctrine\QueryBuilderHelper;
use App\Entity\Camp;
use App\Entity\ContentNode;
use App\Entity\User;
use Doctrine\ORM\QueryBuilder;

trait FiltersByContentNode {
    use FiltersByCampCollaboration;

    /**
     * Applies a filter that builds both ownership-paths for content nodes (via Activity and via Category)
     * and then applies the normal campCollaboration filter on top of it (which checks for an active campCollaboration
     * of the given user).
     *
     * Assumes the queryBuilder already knows how to get to the corresponding contentNode. You need to pass
     * the alias of the contentNode as the third argument.
     */
    protected function filterByContentNode(QueryBuilder $queryBuilder, User $user, string $contentNodeAlias): void {
        $em = $queryBuilder->getEntityManager();

        $campQry = $em->createQueryBuilder();
        $campQry->from(Camp::class, 'camp')->select('camp');
        $this->filterByCampCollaboration($campQry, $user);

        $rootQry = $em->createQueryBuilder();
        $rootQry->from(ContentNode::class, 'cn')->select('cn');
        $rootQry->join('cn.campRootContentNodes', 'r');
        $rootQry->where($rootQry->expr()->in('r.camp', $campQry->getDQL()));
        QueryBuilderHelper::copyParameters($rootQry, $campQry);

        $queryBuilder->andWhere($queryBuilder->expr()->in("{$contentNodeAlias}.root", $rootQry->getDQL()));
        QueryBuilderHelper::copyParameters($queryBuilder, $rootQry);
    }
}
