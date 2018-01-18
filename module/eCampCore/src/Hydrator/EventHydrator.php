<?php

namespace eCamp\Core\Hydrator;

use eCamp\Api\Collection\EventInstanceCollection;
use eCamp\Api\Collection\EventPluginCollection;
use eCamp\Core\Entity\Event;
use Zend\Hydrator\HydratorInterface;

class EventHydrator implements HydratorInterface
{
    /**
     * @param object $object
     * @return array
     */
    public function extract($object) {
        /** @var Event $event */
        $event = $object;
        return [
            'id' => $event->getId(),
            'title' => $event->getTitle(),
            'camp' => $event->getCamp(),

            'event_category' => $event->getEventCategory(),

            'event_plugins' => new EventPluginCollection($event->getEventPlugins()),
            'event_instances' => new EventInstanceCollection($event->getEventInstances()),
        ];
    }

    /**
     * @param array $data
     * @param object $object
     * @return object
     */
    public function hydrate(array $data, $object) {
        /** @var Event $event */
        $event = $object;

        $event->setTitle($data['title']);

        return $event;
    }
}