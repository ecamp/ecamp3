<?php

namespace eCamp\Core\EntityService;

use Doctrine\ORM\ORMException;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\Day;
use eCamp\Core\Entity\Event;
use eCamp\Core\Entity\Period;
use eCamp\Core\Hydrator\EventInstanceHydrator;
use eCamp\Core\Entity\EventInstance;
use eCamp\Lib\Acl\NoAccessException;
use eCamp\Lib\Service\ServiceUtils;
use ZF\ApiProblem\ApiProblem;

class EventInstanceService extends AbstractEntityService {
    public function __construct(ServiceUtils $serviceUtils) {
        parent::__construct(
            $serviceUtils,
            EventInstance::class,
            EventInstanceHydrator::class
        );
    }

    protected function fetchAllQueryBuilder($params = []) {
        $q = parent::fetchAllQueryBuilder($params);
        $q->join('row.period', 'p');
        $q->andWhere($this->createFilter($q, Camp::class, 'p', 'camp'));

        $period = $this->getEntityFromData(Period::class, $params, 'period');
        if ($period != null) {
            $q->andWhere('row.period = :period');
            $q->setParameter('period', $period);
        }

        /** @var Day $day */
        $day = $this->getEntityFromData(Day::class, $params, 'day');
        if ($day != null) {
            $period = $day->getPeriod();
            $q->andWhere('row.period = :dayPeriod');
            $q->setParameter('dayPeriod', $period);

            $q->andWhere('row.start < :dayEnd');
            $q->setParameter('dayEnd', 24 * 60 * ($day->getDayOffset() + 1));
            $q->andWhere('(row.start + row.length) > :dayStart');
            $q->setParameter('dayStart', 24 * 60 * $day->getDayOffset());
        }

        return $q;
    }

    protected function fetchQueryBuilder($id) {
        $q = parent::fetchQueryBuilder($id);
        $q->join('row.period', 'p');
        $q->andWhere($this->createFilter($q, Camp::class, 'p', 'camp'));

        return $q;
    }


    /**
     * @param mixed $data
     * @return EventInstance|ApiProblem
     * @throws ORMException
     * @throws NoAccessException
     */
    public function create($data) {
        /** @var Period $period */
        $period = $this->getEntityFromData(Period::class, $data, 'period');

        /** @var Event $event */
        $event = $this->getEntityFromData(Event::class, $data, 'event');

        /** @var EventInstance $eventInstance */
        $eventInstance = parent::create($data);
        if ($eventInstance instanceof ApiProblem) {
            return $eventInstance;
        }

        $period->addEventInstance($eventInstance);
        $event->addEventInstance($eventInstance);

        return $eventInstance;
    }
}
