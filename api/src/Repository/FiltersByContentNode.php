<?php

namespace App\Repository;

use App\Entity\Activity;
use App\Entity\Category;
use App\Entity\ContentNode;
use App\Entity\User;
use Doctrine\ORM\Query\Expr\Join;
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
        $rootQry = $queryBuilder->getEntityManager()->createQueryBuilder();
        $rootQry->from(ContentNode::class, 'cn')->select('cn');

        // assuming owner is an Activity
        $rootQry->leftJoin(Activity::class, 'cn_activity', Join::WITH, 'cn_activity.rootContentNode = cn.id');

        /*
         *   If owner is an Activity --> cn_activity.category is not null
         *   If owner is a Category --> cn_category.rootContentNode = root.id is a match
         */
        $rootQry->join(Category::class, 'cn_category', Join::WITH, 'cn_category = cn_activity.category OR cn_category.rootContentNode = cn');

        // load owning camp via category
        $rootQry->join('cn_category.camp', 'cn_camp');

        $this->filterByCampCollaboration($rootQry, $user, 'cn_camp');

        $queryBuilder->andWhere($queryBuilder->expr()->in("{$contentNodeAlias}.root", $rootQry->getDQL()));
        $this->queryBuilderAddParameters($queryBuilder, $rootQry);
    }
}
