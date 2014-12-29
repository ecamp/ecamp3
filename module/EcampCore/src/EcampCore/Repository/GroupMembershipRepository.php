<?php

namespace EcampCore\Repository;

use Doctrine\ORM\EntityRepository;
use EcampCore\Entity\User;
use EcampCore\Entity\Group;
use EcampCore\Entity\GroupMembership;

use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use Zend\Paginator\Paginator;

class GroupMembershipRepository extends EntityRepository
{

    public function getCollection($criteria)
    {
        $q = $this->createQueryBuilder('gm');

        if (isset($criteria["group"]) && !is_null($criteria["group"])) {
            $q->andWhere('gm.group = :group');
            $q->setParameter('group', $criteria["group"]);
        }

        if (isset($criteria["user"]) && !is_null($criteria["user"])) {
            $q->andWhere('gm.user = :user');
            $q->setParameter('user', $criteria["user"]);
        }

        if (isset($criteria["role"]) && !is_null($criteria["role"])) {
            $q->andWhere('gm.role = :role');
            $q->setParameter('role', $criteria["role"]);
        }

        if (isset($criteria["status"]) && !is_null($criteria["status"])) {
            $q->andWhere('gm.status = :status');
            $q->setParameter('status', $criteria["status"]);
        }

        return new Paginator(new PaginatorAdapter(new ORMPaginator($q->getQuery())));
    }

    /**
     * @param Group $group
     * @param User $user
     * @return GroupMembership
     */
    public function findByGroupAndUser(Group $group, User $user)
    {
        $query = $this->createQueryBuilder('gm')
                    ->where("gm.group = :group")
                    ->andWhere("gm.user = :user")
                    ->setParameter('group', $group->getId())
                    ->setParameter('user', $user->getId())
                    ->setMaxResults(1)
                    ->getQuery();

        $gm = $query->getResult();

        return (count($gm) > 0) ? $gm[0] : null;
    }

    public function findByUser(User $user)
    {
        $query = $this->createQueryBuilder('gm')
            ->join("gm.group", "g")
            ->where("gm.user = :user")
            ->andWhere("gm.status = :status")
            ->setParameter('user', $user->getId())
            ->setParameter('status', GroupMembership::STATUS_ESTABLISHED)
            ->orderBy("gm.group", "asc")
            ->getQuery();

        return $query->getResult();
    }

}
