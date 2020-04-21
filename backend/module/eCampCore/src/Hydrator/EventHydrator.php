<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\Event;
use eCamp\Lib\Entity\EntityLink;
use eCamp\Lib\Entity\EntityLinkCollection;
use eCamp\Lib\Hydrator\Util;
use eCampApi\V1\Rest\EventInstance\EventInstanceCollection;
use eCampApi\V1\Rest\EventPlugin\EventPluginCollection;
use Zend\Hydrator\HydratorInterface;

class EventHydrator implements HydratorInterface {
    public static function HydrateInfo() {
        return [
            'event_category' => Util::Entity(function(Event $e) {
                return $e->getEventCategory();
            }),
            'event_instances' => Util::Collection(function (Event $e) {
                return new EventInstanceCollection($e->getEventInstances());
            }, null),
            'event_plugins' => Util::Collection(function (Event $e) {
                return new EventPluginCollection($e->getEventPlugins());
            })
        ];
    }

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

            'camp' => new EntityLink($event->getCamp()),
            'event_category' => EntityLink::Create($event->getEventCategory()),

            'event_instances' => new EntityLinkCollection($event->getEventInstances()),

            'event_plugins' => new EntityLinkCollection($event->getEventPlugins())

//            'event_plugins' => Link::factory([
//                'rel' => 'event_plugins',
//                'route' => [
//                    'name' => 'e-camp-api.rest.doctrine.event-plugin',
//                    'options' => [ 'query' => [ 'event_id' => $event->getId() ] ]
//                ]
//            ]),
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
