<?php

namespace EcampCore\Repository;

use Doctrine\ORM\EntityRepository;

use EcampCore\Entity\UserCamp;

class CampRepository
    extends EntityRepository
{

    public function findForApi(array $criteria)
    {
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

        if (! (isset($criteria['past']) && $criteria['past'])) {
            // hide past camps
            $q->andWhere($q->expr()->exists(
                "select 1
                 from 	EcampCore\Entity\Period p
                 left outer join   EcampCore\Entity\Day d with d.period = p
                 where 	p.camp = c
                 group by p.id, p.start
                 having DATE_ADD(p.start, count(d.id), 'day') > CURRENT_DATE()"
            ));
        }

        if (isset($criteria["offset"]) && !is_null($criteria["offset"])) {
            $q->setFirstResult($criteria["offset"]);
            $q->setMaxResults(100);
        }
        if (isset($criteria["limit"]) && !is_null($criteria["limit"])) {
            $q->setMaxResults($criteria["limit"]);
        }

        return $q->getQuery()->getResult();
    }

    public function findUserCamps($userId)
    {
        $q = $this->_em->createQuery(
            "	SELECT 	c" .
            "	FROM 	EcampCore\Entity\Camp c" .
            "	WHERE 	c.owner = :userId" .
            "	OR		EXISTS (" .
            "		SELECT 	1" .
            "		FROM	EcampCore\Entity\UserCamp uc" .
            "		WHERE	uc.camp = c.id" .
            "		AND		uc.user = :userId" .
            "		AND		uc.role > :role" .
            "	)"
        );

        $q->setParameter('userId', 	$userId);
        $q->setParameter('role', 	UserCamp::ROLE_NONE);

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

    public function findGroupCamps($groupId)
    {
        return $this->findBy(array('group' => $groupId));
    }

    public function findGroupCamp($groupId, $campName)
    {
        return $this->findOneBy(array('group' => $groupId, 'name' => $campName));
    }
}
