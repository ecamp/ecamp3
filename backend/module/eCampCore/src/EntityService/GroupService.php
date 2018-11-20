<?php

namespace eCamp\Core\EntityService;

use eCamp\Core\Entity\Group;
use eCamp\Core\Entity\GroupMembership;
use eCamp\Core\Entity\User;
use eCamp\Core\Hydrator\GroupHydrator;
use eCamp\Lib\Service\ServiceUtils;
use Zend\Authentication\AuthenticationService;

class GroupService extends AbstractEntityService {
    public function __construct(ServiceUtils $serviceUtils, AuthenticationService $authenticationService) {
        parent::__construct(
            $serviceUtils,
            $authenticationService,
            Group::class,
            GroupHydrator::class
        );
    }


    public function fetchByParentGroup(Group $group = null) {
        $q = $this->findCollectionQueryBuilder(Group::class, 'g');
        if ($group == null) {
            $q->where($q->expr()->isNull('g.parent'));
        } else {
            $q->where('g.parent = :group');
            $q->setParameter('group', $group);
        }

        return $this->getQueryResult($q);
    }

    public function fetchByUser(User $user = null) {
        $memQ = $this->findCollectionQueryBuilder(GroupMembership::class, 'gm');
        $memQ->join('gm.group', 'g');
        $memQ->where('gm.user = :user', 'gm.status = :status');
        $memQ->select('g');

        $q = $this->fetchAllQueryBuilder();
        $q->where(
            $q->expr()->in('row.id', $memQ->getDQL())
        );

        $user = $user ?: $this->getAuthUser();
        $q->setParameter('user', $user);
        $q->setParameter('status', GroupMembership::STATUS_ESTABLISHED);

        return $this->getQueryResult($q);
    }
}
