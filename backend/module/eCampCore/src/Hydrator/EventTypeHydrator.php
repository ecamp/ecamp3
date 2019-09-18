<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\EventType;
use eCampApi\V1\Rest\EventTypePlugin\EventTypePluginCollection;
use Zend\Hydrator\HydratorInterface;

class EventTypeHydrator implements HydratorInterface {
    /**
     * @param object $object
     * @return array
     */
    public function extract($object) {
        /** @var EventType $eventType */
        $eventType = $object;
        return [
            'id' => $eventType->getId(),
            'name' => $eventType->getName(),
            'default_color' => $eventType->getDefaultColor(),
            'default_numbering_style' => $eventType->getDefaultNumberingStyle(),
            'event_type_plugins' =>  new EventTypePluginCollection($eventType->getEventTypePlugins()),
//            'event_type_factories' => new EventTypeFactoryCollection($eventType->getEventTypeFactories()),
//            'event_templates' => new EventTemplateCollection($eventType->getEventTemplates()),
        ];
    }

    /**
     * @param array $data
     * @param object $object
     * @return object
     */
    public function hydrate(array $data, $object) {
        /** @var EventType $eventType */
        $eventType = $object;

        $eventType->setName($data['name']);
        $eventType->setDefaultColor($data['default_color']);
        $eventType->setDefaultNumberingStyle($data['default_numbering_style']);

        return $eventType;
    }
}
