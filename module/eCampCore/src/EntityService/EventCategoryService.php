<?php

namespace eCamp\Core\EntityService;

use Doctrine\ORM\ORMException;
use eCamp\Core\Hydrator\EventCategoryHydrator;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\EventCategory;
use eCamp\Core\Entity\EventType;
use eCamp\Lib\Acl\NoAccessException;
use ZF\ApiProblem\ApiProblem;

class EventCategoryService extends AbstractEntityService {
    public function __construct() {
        parent::__construct(
            EventCategory::class,
            EventCategoryHydrator::class
        );
    }

    /**
     * @param mixed $data
     * @return EventCategory|ApiProblem
     * @throws ORMException
     * @throws NoAccessException
     */
    public function create($data) {
        /** @var EventCategory $eventCategory */
        $eventCategory = parent::create($data);

        /** @var EventType $eventType */
        $eventType = $this->findEntity(EventType::class, $data->event_type_id);
        $eventCategory->setEventType($eventType);

        /** @var Camp $camp */
        $camp = $this->findEntity(Camp::class, $data->camp_id);
        $camp->addEventCategory($eventCategory);

        return $eventCategory;
    }
}
