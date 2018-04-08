<?php

namespace eCamp\Core\Service;

use eCamp\Core\Entity\GroupMembership;
use eCamp\Core\Entity\User;
use eCamp\Core\Hydrator\GroupHydrator;
use eCamp\Core\Entity\Group;
use eCamp\Lib\Service\BaseService;

class GroupService extends BaseService
{
    public function __construct(GroupHydrator $groupHydrator)
    {
        parent::__construct($groupHydrator, Group::class);
    }


    public function fetchByParentGroup(Group $group = null)
    {
        $q = $this->findCollectionQueryBuilder(Group::class, 'g');
        if ($group == null) {
            $q->where($q->expr()->isNull('g.parent'));
        } else {
            $q->where('g.parent = :group');
            $q->setParameter('group', $group);
        }

        return $this->getQueryResult($q);
    }

    public function fetchByUser(User $user = null)
    {
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
