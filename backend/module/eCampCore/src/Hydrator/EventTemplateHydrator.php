<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\EventTemplate;
use Zend\Hydrator\HydratorInterface;

class EventTemplateHydrator implements HydratorInterface {
    public static function HydrateInfo() {
        return [
        ];
    }

    /**
     * @param object $object
     *
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
            //            'containers' => new EventTemplateContainerCollection($eventTemplate->getEventTemplateContainers()),
        ];
    }

    /**
     * @param object $object
     *
     * @return object
     */
    public function hydrate(array $data, $object) {
        // @var EventTemplate $eventTemplate
        return $object;
    }
}
