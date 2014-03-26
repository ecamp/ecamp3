<?php

namespace EcampCore\Repository;

use EcampCore\Entity\User;
use EcampCore\Entity\UserRelationship;

use Doctrine\ORM\EntityRepository;

use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use Zend\Paginator\Paginator;
use Zend\Validator\EmailAddress;
use Doctrine\ORM\Query\Expr\Join;

class UserRepository extends EntityRepository
{

    public function getCollection($criteria)
    {
        $q = $this->createQueryBuilder('u');

        if (isset($criteria["search"]) && !is_null($criteria["search"])) {
            $q->andWhere(
                $q->expr()->orX(
                    "u.username like concat(:search, '%')",
                    "u.scoutname like concat(:search, '%')",
                    "u.firstname like concat(:search, '%')",
                    "u.surname like concat(:search, '%')"
                )
            );
            $q->setParameter('search', $criteria["search"]);
        }

        return new Paginator(new PaginatorAdapter(new ORMPaginator($q->getQuery())));
    }

    /**
     * @param  string                    $search
     * @return \Zend\Paginator\Paginator
     */
    public function getSearchResult($search)
    {
        $tokens = explode(" ", $search);
        $tokens = array_filter($tokens);

        $q = $this->createQueryBuilder('u');

        $userQ = $this->createQueryBuilder('uu');
           $userQ->where('u.id = uu.id');

        $campQ = $this->createQueryBuilder('cu');
        $campQ->join('cu.collaborations', 'col');
        $campQ->join('col.camp', 'camp');
        $campQ->where('u.id = cu.id');

        $groupQ = $this->createQueryBuilder('gu');
        $groupQ->join('gu.memberships', 'mem');
        $groupQ->join('mem.group', 'grp');
        $groupQ->where('u.id = gu.id');

        foreach ($tokens as $key => $token) {
            $userQ->andWhere(
                $userQ->expr()->orX(
                    'uu.username like ' . $userQ->expr()->concat(':uu_user_token' . $key, "'%'"),
                    'uu.scoutname like ' . $userQ->expr()->concat(':uu_scout_token' . $key, "'%'"),
                    'uu.firstname like ' . $userQ->expr()->concat(':uu_first_token' . $key, "'%'"),
                    'uu.surname like ' . $userQ->expr()->concat(':uu_sur_token' . $key, "'%'"),
                    'uu.email like ' . $userQ->expr()->concat(':uu_mail_token' . $key, "'%'")
                )
            );
            $q->setParameter('uu_user_token' . $key, $token);
            $q->setParameter('uu_scout_token' . $key, $token);
            $q->setParameter('uu_first_token' . $key, $token);
            $q->setParameter('uu_sur_token' . $key, $token);
            $q->setParameter('uu_mail_token' . $key, $token);

            $campQ->andWhere(
                $campQ->expr()->orX(
                    'cu.username like ' . $userQ->expr()->concat(':cu_user_token' . $key, "'%'"),
                    'cu.scoutname like ' . $userQ->expr()->concat(':cu_scout_token' . $key, "'%'"),
                    'cu.firstname like ' . $userQ->expr()->concat(':cu_first_token' . $key, "'%'"),
                    'cu.surname like ' . $userQ->expr()->concat(':cu_sur_token' . $key, "'%'"),
                    'cu.email like ' . $userQ->expr()->concat(':cu_mail_token' . $key, "'%'"),
                    'camp.name like ' . $userQ->expr()->concat(':cu_cmp_name_token' . $key, "'%'"),
                    'camp.title like ' . $userQ->expr()->concat(':cu_cmp_title_token' . $key, "'%'"),
                    'camp.motto like ' . $userQ->expr()->concat(':cu_cmp_motto_token' . $key, "'%'")
                )
            );

            $q->setParameter('cu_user_token' . $key, $token);
            $q->setParameter('cu_scout_token' . $key, $token);
            $q->setParameter('cu_first_token' . $key, $token);
            $q->setParameter('cu_sur_token' . $key, $token);
            $q->setParameter('cu_mail_token' . $key, $token);
            $q->setParameter('cu_cmp_name_token' . $key, $token);
            $q->setParameter('cu_cmp_title_token' . $key, $token);
            $q->setParameter('cu_cmp_motto_token' . $key, $token);

            $groupQ->andWhere(
                $campQ->expr()->orX(
                    'gu.username like ' . $groupQ->expr()->concat(':gu_user_token' . $key, "'%'"),
                    'gu.scoutname like ' . $groupQ->expr()->concat(':gu_scout_token' . $key, "'%'"),
                    'gu.firstname like ' . $groupQ->expr()->concat(':gu_first_token' . $key, "'%'"),
                    'gu.surname like ' . $groupQ->expr()->concat(':gu_sur_token' . $key, "'%'"),
                    'gu.email like ' . $groupQ->expr()->concat(':gu_mail_token' . $key, "'%'"),
                    'grp.name like ' . $groupQ->expr()->concat(':gu_grp_name_token' . $key, "'%'"),
                    'grp.description like ' . $groupQ->expr()->concat(':gu_grp_desc_token' . $key, "'%'")
                )
            );

            $q->setParameter('gu_user_token' . $key, $token);
            $q->setParameter('gu_scout_token' . $key, $token);
            $q->setParameter('gu_first_token' . $key, $token);
            $q->setParameter('gu_sur_token' . $key, $token);
            $q->setParameter('gu_mail_token' . $key, $token);
            $q->setParameter('gu_grp_name_token' . $key, $token);
            $q->setParameter('gu_grp_desc_token' . $key, $token);
        }

        $q->where($q->expr()->orX(
            $q->expr()->exists($userQ->getDQL()),
            $q->expr()->exists($campQ->getDQL()),
            $q->expr()->exists($groupQ->getDQL())
        ));

        return new Paginator(new PaginatorAdapter(new ORMPaginator($q->getQuery())));
    }

    public function findByIdentifier($identifier)
    {
        $user = null;
        $mailValidator = new EmailAddress();

        if ($identifier) {
            if ($mailValidator->isValid($identifier)) {
                $user = $this->findOneBy(array('email' => $identifier));
            } else {
                $user = $this->find($identifier);
            }
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
