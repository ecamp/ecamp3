<?php

namespace eCamp\Core\EntityService;

use eCamp\Core\Entity\Camp;
use eCamp\Core\Hydrator\EventHydrator;
use eCamp\Core\Entity\Event;

class EventService extends AbstractEntityService
{
    public function __construct()
    {
        parent::__construct(
            Event::class,
            EventHydrator::class
        );
    }

    protected function fetchAllQueryBuilder($params = [])
    {
        $q = parent::fetchAllQueryBuilder($params);
        $q->andWhere($this->createFilter($q, Camp::class, 'row', 'camp'));

        return $q;
    }

    protected function fetchQueryBuilder($id)
    {
        $q = parent::fetchQueryBuilder($id);
        $q->andWhere($this->createFilter($q, Camp::class, 'row', 'camp'));

        return $q;
    }

}
