<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\EventTemplateContainer;
use Zend\Hydrator\HydratorInterface;

class EventTemplateContainerHydrator implements HydratorInterface {

    public static function HydrateInfo() {
        return [
        ];
    }

    /**
     * @param object $object
     * @return array
     */
    public function extract($object) {
        /** @var EventTemplateContainer $eventTemplateContainer */
        $eventTemplateContainer = $object;
        return [
            'id' => $eventTemplateContainer->getId(),
            'event_template' => $eventTemplateContainer->getEventTemplate(),
            'filename' => $eventTemplateContainer->getFilename(),
            'container_name' => $eventTemplateContainer->getContainerName()
        ];
    }

    /**
     * @param array $data
     * @param object $object
     * @return object
     */
    public function hydrate(array $data, $object) {
        /** @var EventTemplateContainer $eventTemplateContainer */
        $eventTemplateContainer = $object;

        return $eventTemplateContainer;
    }
}
