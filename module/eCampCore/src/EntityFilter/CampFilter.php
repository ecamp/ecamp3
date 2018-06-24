<?php

namespace eCamp\Core\EntityFilter;

use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\CampCollaboration;

class CampFilter extends BaseFilter {
    public function create(QueryBuilder $q, $alias, $field) {
        $collQ = $this->findCollectionQueryBuilder(CampCollaboration::class, 'cc');
        $collQ->join('cc.camp', 'camp');
        $collQ->select('camp');
        $collQ->where('cc.user = :f_auth_user', 'cc.status = :f_cc_established');

        $campQ = $this->findCollectionQueryBuilder(Camp::class, 'c');
        $campQ->orWhere(
            //$campQ->expr()->eq('1', '1'),
            $campQ->expr()->in('c.id', $collQ->getDQL()),
            $campQ->expr()->eq('c.owner', ':f_auth_user')
        );

        $authUser = $this->authUserProvider->getAuthUser();
        $established = CampCollaboration::STATUS_ESTABLISHED;

        $q->setParameter('f_auth_user', $authUser);
        $q->setParameter('f_cc_established', $established);

        return new Expr\Func($alias . '.' . $field . ' IN', (array)$campQ->getDQL());
    }
}
