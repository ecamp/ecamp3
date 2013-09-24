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

        return new Paginator(new PaginatorAdapter(new ORMPaginator($q->getQuery())));
    }

    /**
     * @param  Group           $group
     * @param  User            $user
     * @return GroupMembership
     */
    public function findByGroupAndUser(Group $group, User $user)
    {
        $query = $this->createQueryBuilder('gm')
                    ->where("gm.group = '" . $group->getId() . "'")
                    ->andWhere("gm.user = '" . $user->getId() . "'")
                    ->setMaxResults(1)
                    ->getQuery();

        $gm = $query->getResult();

        return (count($gm) > 0) ? $gm[0] : null;
    }

    public function findByUser(User $user)
    {
        $query = $this->createQueryBuilder('gm')
            ->where("gm.user = '" . $user->getId() . "'")
            ->getQuery();

        $gm = $query->getResult();
    }

}
