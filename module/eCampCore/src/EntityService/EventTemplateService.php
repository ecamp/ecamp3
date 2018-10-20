<?php

namespace eCamp\Core\EntityService;

use eCamp\Core\Entity\Event;
use eCamp\Core\Entity\EventCategory;
use eCamp\Core\Entity\EventType;
use eCamp\Core\Entity\Medium;
use eCamp\Core\Hydrator\EventTemplateHydrator;
use eCamp\Core\Entity\EventTemplate;
use eCamp\Lib\Service\ServiceUtils;

class EventTemplateService extends AbstractEntityService {
    public function __construct(ServiceUtils $serviceUtils) {
        parent::__construct(
            $serviceUtils,
            EventTemplate::class,
            EventTemplateHydrator::class
        );
    }


    protected function fetchAllQueryBuilder($params = []) {
        $q = parent::fetchAllQueryBuilder($params);

        $event = $this->getEntityFromData(Event::class, $params, 'event');
        if ($event != null) {
            /** @var Event $event */
            $eventType = $event->getEventType();
            $q->andWhere('row.eventType = :eventEventType');
            $q->setParameter('eventEventType', $eventType);
        }

        $eventCategory = $this->getEntityFromData(EventCategory::class, $params, 'event_category');
        if ($eventCategory != null) {
            /** @var EventCategory $eventCategory */
            $eventType = $eventCategory->getEventType();
            $q->andWhere('row.eventType = :eventCategoryEventType');
            $q->setParameter('eventCategoryEventType', $eventType);
        }

        $eventType = $this->getEntityFromData(EventType::class, $params, 'event_type');
        if ($eventType != null) {
            $q->andWhere('row.eventType = :eventType');
            $q->setParameter('eventType', $eventType);
        }

        $medium = $this->getEntityFromData(Medium::class, $params, 'medium');
        if ($medium != null) {
            $q->andWhere('row.medium = :medium');
            $q->setParameter('medium', $medium);
        }

        return $q;
    }
}
