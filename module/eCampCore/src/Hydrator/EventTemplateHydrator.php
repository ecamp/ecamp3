<?php

namespace eCamp\Core\Hydrator;

use eCamp\Api\Collection\EventTemplateContainerCollection;
use eCamp\Core\Entity\EventTemplate;
use Zend\Hydrator\HydratorInterface;

class EventTemplateHydrator implements HydratorInterface
{
    /**
     * @param object $object
     * @return array
     */
    public function extract($object) {
        /** @var EventTemplate $eventTemplate */
        $eventTemplate = $object;
        return [
            'id' => $eventTemplate->getId(),
            'medium' => $eventTemplate->getMedium(),
            'event_type' => $eventTemplate->getEventType(),
            'filename' => $eventTemplate->getFilename(),
            'containers' => new EventTemplateContainerCollection($eventTemplate->getEventTemplateContainers()),
        ];
    }

    /**
     * @param array $data
     * @param object $object
     * @return object
     */
    public function hydrate(array $data, $object) {
        /** @var EventTemplate $eventTemplate */
        $eventTemplate = $object;

        return $eventTemplate;
    }
}