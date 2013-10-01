<?php

namespace EcampCore\Repository;

use Doctrine\ORM\EntityRepository;
use EcampCore\Entity\GroupMembership;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use Zend\Paginator\Paginator;

class GroupRepository extends EntityRepository
{

    public function getCollection($criteria)
    {
        $q = $this->createQueryBuilder('g');

        if (isset($criteria["group"]) && !is_null($criteria["group"])) {
            $q->andWhere('g.parent = :group');
            $q->setParameter('group', $criteria["group"]);
        }

        return new Paginator(new PaginatorAdapter(new ORMPaginator($q->getQuery())));
    }

    public function findRootGroups()
    {
        return $this->createQueryBuilder("g")
                ->where("g.parent IS NULL ")
                ->getQuery()->getResult();
    }

    public function findUserGroups($userId)
    {
        $q = $this->_em->createQuery(
                "	SELECT 	g" .
                "	FROM 	EcampCore\Entity\Group g" .
                "	WHERE 	EXISTS (" .
                "		SELECT 	1" .
                "		FROM	EcampCore\Entity\GroupMembership gm" .
                "		WHERE	gm.group = g.id" .
                "		AND		gm.user = :userId" .
                "		AND		gm.status = '" . GroupMembership::STATUS_ESTABLISHED . "'" .
                "	)"
        );

        $q->setParameter('userId', 	$userId);

        return $q->getResult();
    }

}
