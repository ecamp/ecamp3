<?php

namespace App\Repository;

use App\Entity\Activity;
use App\Entity\CampRootContentNode;
use App\Entity\Category;
use App\Entity\ContentNode;
use App\Entity\User;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
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
        /*
        c0_.rootId IN (
            SELECT
            c8_.id
            FROM
            content_node c8_
            LEFT JOIN activity a9_ ON (a9_.rootContentNodeId = c8_.id)
            INNER JOIN category c10_ ON (
                c10_.id = a9_.categoryId
                OR c10_.rootContentNodeId = c8_.id
            )
            INNER JOIN camp c11_ ON c10_.campId = c11_.id
            LEFT JOIN camp_collaboration c12_ ON c11_.id = c12_.campId
            AND (
                c12_.userId = ?
                AND c12_.status = ?
            )
            WHERE
            (
                c12_.id IS NOT NULL
                OR c11_.isPrototype = ?
            )
            AND c8_.strategy IN (
                'contentnode', 'responsivelayout',
                'storyboard', 'multiselect', 'materialnode',
                'columnlayout', 'singletext'
            )
        )


        c0_.rootId in (
            select a.rootContentNodeId
            from (
                select a.campId, a.rootContentNodeId
                from activity
                union all
                select ca.campId, ca.rootContentNodeId
                from category
            ) a
            join camp c on c.id = a.campId
            LEFT JOIN camp_collaboration c12_ ON c11_.id = c12_.campId
            AND (
                c12_.userId = ?
                AND c12_.status = ?
            )
            WHERE
            (
                c12_.id IS NOT NULL
                OR c11_.isPrototype = ?
            )
        )
        */

        $rootQry = $queryBuilder->getEntityManager()->createQueryBuilder();
        $rootQry->from(ContentNode::class, 'cn')->select('cn');
        $rootQry->join('cn.rootContentNodes', 'r');
        $rootQry->join('r.camp', 'camp');
        $this->filterByCampCollaboration($rootQry, $user);

        // $rootQry = $queryBuilder->getEntityManager()->createQueryBuilder();
        // $rootQry->from(ContentNode::class, 'cn')->select('cn');

        // assuming owner is an Activity
        // $rootQry->leftJoin(Activity::class, 'cn_activity', Join::WITH, 'cn_activity.rootContentNode = cn.id');

        /*
         *   If owner is an Activity --> cn_activity.category is not null
         *   If owner is a Category --> cn_category.rootContentNode = root.id is a match
         */
        // $rootQry->join(Category::class, 'cn_category', Join::WITH, 'cn_category = cn_activity.category OR cn_category.rootContentNode = cn');

        // load owning camp via category
        // $rootQry->join('cn_category.camp', 'cn_camp');

        // $this->filterByCampCollaboration($rootQry, $user, 'cn_camp');

        $queryBuilder->andWhere($queryBuilder->expr()->in("{$contentNodeAlias}.root", $rootQry->getDQL()));
        $this->queryBuilderAddParameters($queryBuilder, $rootQry);
    }
}
