<?php

namespace EcampCore\Repository;

use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use EcampCore\Entity\Day;
use EcampCore\Printable\EventInstance;
use Zend\Paginator\Paginator;

/**
 * Class EventInstanceRepository
 * @package EcampCore\Repository
 *
 * @method EventInstance find($id)
 */
class EventInstanceRepository extends EntityRepository
{

    public function getCollection(array $criteria)
    {
        $q = $this->createQueryBuilder('ei');

        if (isset($criteria['event']) && !is_null($criteria['event'])) {
            $q->andWhere('ei.event = :event');
            $q->setParameter('event', $criteria['event']);
        }

        if (isset($criteria['camp']) && !is_null($criteria['camp'])) {
            $q->andWhere(
                $q->expr()->exists("
                    select	1
                    from	EcampCore\Entity\Camp c
                    join	EcampCore\Entity\Event e with e.camp = c
                    where 	ei.event = e
                    and		c = :camp
                ")
            );
            $q->setParameter('camp', $criteria['camp']);
        }

        if (isset($criteria['period']) && !is_null($criteria['period'])) {
            $q->andWhere('ei.period = :period');
            $q->setParameter('period', $criteria['period']);
        }

        if (isset($criteria['day']) && !is_null($criteria['day'])) {
            $q->andWhere(
                $q->expr()->exists("
                    select	1
                    from	EcampCore\Entity\Day d
                    join	EcampCore\Entity\Period p with d.period = p
                    where 	ei.period = p
                    and		ei.minOffsetStart <= (d.dayOffset + 1) * 1440
                    and		ei.minOffsetEnd >= d.dayOffset * 1440
                    and		d = :day
                ")
            );
            $q->setParameter('day', $criteria['day']);
        }

        return new Paginator(new PaginatorAdapter(new ORMPaginator($q->getQuery())));
    }

    public function findByDay($day)
    {
        if (! $day instanceof Day) {
            $day = $this->_em->find('EcampCore\Entity\Day', $day);
        }

        if ($day != null) {
            $dayStart 	= $day->getDayOffset() * 24 * 60;
            $dayEnd		= ($day->getDayOffset() + 1) * 24 * 60;

            $q = $this->createQueryBuilder('ei')
                ->where('ei.period = :periodId')
                ->andWhere('ei.minOffsetStart < :dayEnd')
                ->andWhere('ei.minOffsetEnd > :dayStart')
                ->setParameter('periodId', $day->getPeriod()->getId())
                ->setParameter('dayEnd', $dayEnd)
                ->setParameter('dayStart', $dayStart)
                ->orderBy('ei.minOffsetStart, ei.createdAt');

            return $q->getQuery()->getResult();
        }

        return array();
    }

    public function findByPeriod($periodId)
    {
        return $this->findBy(array('period'=> $periodId));
    }

    public function findByCamp($campId)
    {
        $q = $this->createQueryBuilder('ei')
            ->join('ei.period', 'p')
            ->where('p.camp = :campId')
            ->setParameter('campId', $campId);

        return $q->getQuery()->getResult();

    }

}
