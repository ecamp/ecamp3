<?php

namespace App\Repository;

use App\Entity\Activity;
use App\Entity\Category;
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
        $queryBuilder->innerJoin("{$contentNodeAlias}.root", 'root');

        // assuming owner is an Activity
        $queryBuilder->leftJoin(Activity::class, 'cn_activity', Join::WITH, 'cn_activity.rootContentNode = root.id');

        /*
         *   If owner is an Activity --> cn_activity.category is not null
         *   If owner is a Category --> cn_category.rootContentNode = root.id is a match
         */
        $queryBuilder->join(Category::class, 'cn_category', Join::WITH, 'cn_category.id = cn_activity.category OR cn_category.rootContentNode = root.id');

        // load owning camp via category
        $queryBuilder->join('cn_category.camp', 'cn_camp');

        $this->filterByCampCollaboration($queryBuilder, $user, 'cn_camp');
    }
}
