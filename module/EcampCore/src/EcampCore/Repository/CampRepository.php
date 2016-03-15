<?php

namespace EcampCore\Repository;

use Doctrine\ORM\EntityRepository;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use EcampCore\Entity\AbstractCampOwner;
use Zend\Paginator\Paginator;

use EcampCore\Entity\CampCollaboration;
use EcampCore\Entity\User;

class CampRepository
    extends EntityRepository
{

    public function getCollection(array $criteria)
    {
        /**
            TBD
            startsAfter, startsBefore, endsAfter, endsBefore
            default order: startDate
         */
        $q = $this->createQueryBuilder('c');

        if(isset($criteria['user']) && !is_null($criteria['user'])
        || isset($criteria['collaborator']) && !is_null($criteria['collaborator'])
        ){
            $q->join('c.collaborations', 'cc');
        }

        if (isset($criteria['user']) && !is_null($criteria['user'])) {
            if (isset($criteria['owning_only']) && $criteria['owning_only']) {
                $q->andWhere('c.owner = :user');
            } else {
                $q->andWhere(
                    $q->expr()->orX(
                        $q->expr()->eq('cc.user', ':user'),
                        $q->expr()->eq('c.owner', ':user')
                    )
                );
            }
            $q->setParameter('user', $criteria["user"]);
        }

        if (isset($criteria['owner']) && !is_null($criteria['owner'])) {
            $q->andWhere("c.owner = :owner");
            $q->setParameter('owner', $criteria["owner"]);
        }

        if (isset($criteria['group']) && !is_null($criteria['group'])) {
            $q->andWhere("c.group = :group");
            $q->setParameter('group', $criteria["group"]);
        }

        if (isset($criteria['creator']) && !is_null($criteria['creator'])) {
            $q->andWhere("c.creator = :creator");
            $q->setParameter('creator', $criteria["creator"]);
        }

        if (isset($criteria['collaborator']) && !is_null($criteria['collaborator'])) {
            $q->andWhere('cc.user = :collaborator');
            $q->setParameter('collaborator', $criteria['collaborator']);
        }

        if (isset($criteria['search']) && !is_null($criteria['search'])) {
            $q->andWhere($q->expr()->orX(
                $q->expr()->like('c.name', ':search'),
                $q->expr()->like('c.title', ':search')
            ));

            $search = $criteria['search'];
            $q->setParameter('search', '%' . $search . '%');
        }

        $exprFutureCamp = $q->expr()->exists("
            select 			1
            from 			EcampCore\Entity\Period p
            left outer join EcampCore\Entity\Day d with d.period = p
            where 			p.camp = c
            group by 		p.id, p.start
            having 			DATE_ADD(p.start, count(d.id), 'day') > CURRENT_DATE()
        ");

        if (isset($criteria['mode']) && !is_null($criteria['mode'])) {
            $mode = $criteria['mode'];

            if ($mode == 'past') {
                $q->andWhere($q->expr()->not($exprFutureCamp));
            } elseif ($mode != 'all') {
                $q->andWhere($exprFutureCamp);
            }
        } else {
            if (! (isset($criteria['past']) && $criteria['past'])) {
                // hide past camps
                $q->andWhere($exprFutureCamp);
            }
        }

        return new Paginator(new PaginatorAdapter(new ORMPaginator($q->getQuery())));
    }

    public function findCampsByUser(User $user)
    {
        $q = $this->_em->createQuery(
            "	SELECT 	c" .
            "	FROM 	EcampCore\Entity\Camp c" .
            "	WHERE 	c.owner = :userId" .
            "	OR		EXISTS (" .
            "		SELECT 	1" .
            "		FROM	EcampCore\Entity\CampCollaboration cc" .
            "		WHERE	cc.camp = c.id" .
            "		AND		cc.user = :userId" .
            "		AND		cc.role >= :role" .
            "	)"
        );

        $q->setParameter('userId', 	$user->getId());
        $q->setParameter('role', 	CampCollaboration::ROLE_GUEST);

        return $q->getResult();
    }

    public function findPersonalCamps($userId)
    {
        return $this->findBy(array('owner' => $userId));
    }

    public function findPersonalCamp($userId, $campName)
    {
        return $this->findOneBy(array('owner' => $userId, 'name' => $campName));
    }

    public function findGroupCamps($group)
    {
        return $this->findBy(array('owner' => $group));
    }

    public function findGroupCamp($groupId, $campName)
    {
        return $this->findOneBy(array('group' => $groupId, 'name' => $campName));
    }

    public function findUpcomingCamps(AbstractCampOwner $owner)
    {
        $q = $this->createQueryBuilder('c');
        $q->leftJoin('c.periods', 'cp');
        $q->where($q->expr()->exists("
            select    1
            from      EcampCore\Entity\Period p
            join      EcampCore\Entity\Day d with d.period = p
            where     p.camp = c
            group by  p.id, p.start
            having    DATE_ADD(p.start, count(d.id), 'day') > CURRENT_DATE()
        "));
        $q->andWhere("c.owner = :owner");
        $q->groupBy('c.id');
        $q->orderBy('cp.start', 'ASC');
        $q->setParameter('owner', $owner);

        return $q->getQuery()->getResult();
    }

    public function findPastCamps(AbstractCampOwner $owner)
    {
        $q = $this->createQueryBuilder('c');
        $q->leftJoin('c.periods', 'cp');
        $q->where($q->expr()->not(
            $q->expr()->exists("
                select    1
                from      EcampCore\Entity\Period p
                join      EcampCore\Entity\Day d with d.period = p
                where     p.camp = c
                group by  p.id, p.start
                having    DATE_ADD(p.start, count(d.id), 'day') > CURRENT_DATE()
            ")
        ));
        $q->andWhere("c.owner = :owner");
        $q->groupBy('c.id');
        $q->orderBy('cp.start', 'DESC');
        $q->setParameter('owner', $owner);

        return $q->getQuery()->getResult();
    }
}
