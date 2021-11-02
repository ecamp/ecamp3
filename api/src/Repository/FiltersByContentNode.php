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
        $queryBuilder->innerJoin('root.owner', 'owner');

        // add camp filter in case the ContentNode owner is an Activity
        $queryBuilder->leftJoin(Activity::class, 'activity', Join::WITH, 'activity.id = owner.id');
        $queryBuilder->leftJoin('activity.camp', 'camp_via_activity');
        $this->filterByCampCollaboration($queryBuilder, $user, 'camp_via_activity');

        // add camp filter in case the ContentNode owner is a category
        $queryBuilder->leftJoin(Category::class, 'category', Join::WITH, 'category.id = owner.id');
        $queryBuilder->leftJoin('category.camp', 'camp_via_category');
        $this->filterByCampCollaboration($queryBuilder, $user, 'camp_via_category');
    }
}
