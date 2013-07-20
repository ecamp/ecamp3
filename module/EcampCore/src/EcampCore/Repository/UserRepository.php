<?php

namespace EcampCore\Repository;

use EcampCore\Entity\User;
use EcampCore\Entity\UserRelationship;

use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{

    public function findForApi($criteria)
    {
        $q = $this->createQueryBuilder('u');

        if (isset($criteria["offset"]) && !is_null($criteria["offset"])) {
            $q->setFirstResult($criteria["offset"]);
            $q->setMaxResults(100);
        }
        if (isset($criteria["limit"]) && !is_null($criteria["limit"])) {
            $q->setMaxResults($criteria["limit"]);
        }

        return $q->getQuery()->getResult();
    }

    public function findFriends(User $user)
    {
        $query = $this->createQueryBuilder("u")
                ->innerJoin("u.relationshipFrom", "ur")
                ->where("ur.to = '" . $user->getId() . "'")
                ->andWhere("ur.counterpart is not null")
                ->andWhere("ur.type = " . UserRelationship::TYPE_FRIEND)
                ->getQuery();

        return $query->getResult();
    }

    public function findFriendInvitations(User $user)
    {
        $me = $this->contextProvider->getContext()->getMe();

        $query = $this->createQueryBuilder("u")
                ->innerJoin("u.relationshipFrom", "ur")
                ->where("ur.to = '" . $user->getId() . "'")
                ->andWhere("ur.counterpart is null")
                ->andWhere("ur.type = " . UserRelationship::TYPE_FRIEND)
                ->andWhere("'" . $user->getId() . "' = '" . $me->getId() . "'")
                ->getQuery();

        return $query->getResult();
    }

    public function findFriendRequests(User $user)
    {
        $me = $this->contextProvider->getContext()->getMe();

        $query = $this->createQueryBuilder("u")
                ->innerJoin("u.relationshipTo", "ur")
                ->where("ur.from = '" . $user->getId() . "'")
                ->andWhere("ur.counterpart is null")
                ->andWhere("ur.type = " . UserRelationship::TYPE_FRIEND)
                ->andWhere("'" . $user->getId() . "' = '" . $me->getId() . "'")
                ->getQuery();

        return $query->getResult();
    }

}
