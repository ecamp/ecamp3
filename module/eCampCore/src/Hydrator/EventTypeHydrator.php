<?php

namespace eCamp\Core\Hydrator;

use eCamp\Api\Collection\EventTemplateCollection;
use eCamp\Api\Collection\EventTypeFactoryCollection;
use eCamp\Api\Collection\EventTypePluginCollection;
use eCamp\Core\Entity\EventType;
use Zend\Hydrator\HydratorInterface;

class EventTypeHydrator implements HydratorInterface
{
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
            'event_type_factories' => new EventTypeFactoryCollection($eventType->getEventTypeFactories()),
            'event_templates' => new EventTemplateCollection($eventType->getEventTemplates()),
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

        return $eventType;
    }
}