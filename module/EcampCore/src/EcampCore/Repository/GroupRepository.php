<?php

namespace EcampCore\Repository;

use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;
use EcampCore\Entity\Group;
use EcampCore\Entity\GroupMembership;
use EcampCore\Entity\User;
use Zend\Paginator\Paginator;

/**
 * Class GroupRepository
 * @package EcampCore\Repository
 *
 * @method Group find($id)
 */
class GroupRepository extends BaseRepository
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

    public function findUserGroups($user = null, $role = null)
    {
        $user = $user ?: $this->getAuthenticatedUser();
        $userId = ($user instanceof User ? $user->getId() : $user);

        $q = $this->_em->createQuery(
                "	SELECT 	g" .
                "	FROM 	EcampCore\Entity\Group g" .
                "	WHERE 	EXISTS (" .
                "		SELECT 	1" .
                "		FROM	EcampCore\Entity\GroupMembership gm" .
                "		WHERE	gm.group = g.id" .
                "		AND		gm.user = :userId" .
                "		AND		gm.status = '" . GroupMembership::STATUS_ESTABLISHED . "'" .
($role != null ? "      AND     gm.role = :role" : "") .
                "	)"
        );

        $q->setParameter('userId', 	$userId);

        if ($role != null) {
            $q->setParameter('role', $role);
        }

        return $q->getResult();
    }

}
