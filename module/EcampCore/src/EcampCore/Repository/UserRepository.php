<?php

namespace EcampCore\Repository;

use EcampCore\Entity\User;
use EcampCore\Entity\UserRelationship;

use Doctrine\ORM\EntityRepository;

use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use Zend\Paginator\Paginator;
use Zend\Validator\EmailAddress;

class UserRepository extends EntityRepository
{

    public function getCollection($criteria)
    {
        $q = $this->createQueryBuilder('u');

        return new Paginator(new PaginatorAdapter(new ORMPaginator($q->getQuery())));
    }

    public function findByIdentifier($identifier)
    {
        $user = null;
        $mailValidator = new EmailAddress();

        if ($mailValidator->isValid($identifier)) {
            $user = $this->findOneBy(array('email' => $identifier));
        } else {
            $user = $this->find($identifier);
        }

        return $user;
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
