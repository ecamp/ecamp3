<?php

namespace eCamp\Core\EntityFilter;

use Doctrine\Orm\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\CampCollaboration;

class CampFilter extends BaseFilter
{

    public function create(QueryBuilder $q, $alias, $field) {
        $collQ = $this->findCollectionQueryBuilder(CampCollaboration::class, 'cc');
        $collQ->join('cc.camp', 'camp');
        $collQ->where('cc.user = :user', 'cc.status = :status');
        $collQ->select('camp');

        $campQ = $this->findCollectionQueryBuilder(Camp::class, 'c');
        $campQ->orWhere(
        // Visible Camps
        //$campQ->expr()->eq('1', '1'),
        // AuthUser is the Owner
            $campQ->expr()->eq('c.owner', ':owner'),
            // AuthUser is a Collaborator
            $campQ->expr()->in('c.id', $collQ->getDQL())
        );

        $authUser = $this->authUserProvider->getAuthUser();

        $q->setParameter('owner', $authUser);
        $q->setParameter('user', $authUser);
        $q->setParameter('status', CampCollaboration::STATUS_ESTABLISHED);

        return new Expr\Func($alias . '.' . $field . ' IN', (array)$campQ->getDQL());
    }

}
