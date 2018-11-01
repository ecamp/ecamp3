<?php

namespace eCamp\Core\EntityService;

use Doctrine\ORM\ORMException;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\EventCategory;
use eCamp\Core\Hydrator\EventHydrator;
use eCamp\Core\Entity\Event;
use eCamp\Lib\Acl\NoAccessException;
use eCamp\Lib\Service\ServiceUtils;
use ZF\ApiProblem\ApiProblem;

class EventService extends AbstractEntityService {
    public function __construct(ServiceUtils $serviceUtils) {
        parent::__construct(
            $serviceUtils,
            Event::class,
            EventHydrator::class
        );
    }

    protected function fetchAllQueryBuilder($params = []) {
        $q = parent::fetchAllQueryBuilder($params);
        $q->andWhere($this->createFilter($q, Camp::class, 'row', 'camp'));

        $camp = $this->getEntityFromData(Camp::class, $params, 'camp');
        if ($camp != null) {
            $q->andWhere('row.camp = :camp');
            $q->setParameter('camp', $camp);
        }

        return $q;
    }

    protected function fetchQueryBuilder($id) {
        $q = parent::fetchQueryBuilder($id);
        $q->andWhere($this->createFilter($q, Camp::class, 'row', 'camp'));

        return $q;
    }


    /**
     * @param mixed $data
     * @return Event|ApiProblem
     * @throws ORMException
     * @throws NoAccessException
     */
    public function create($data) {
        /** @var Camp $camp */
        $camp = $this->getEntityFromData(Camp::class, $data, 'camp');

        /** @var EventCategory $eventCategory */
        $eventCategory = $this->getEntityFromData(EventCategory::class, $data, 'event_category');

        /** @var Event $event */
        $event = parent::create($data);
        $camp->addEvent($event);
        $event->setEventCategory($eventCategory);

        return $event;
    }
}
