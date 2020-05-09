<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\Event;
use eCamp\Lib\Entity\EntityLink;
use eCamp\Lib\Entity\EntityLinkCollection;
use eCamp\Lib\Hydrator\Util;
use eCampApi\V1\Rest\EventInstance\EventInstanceCollection;
use Zend\Hydrator\HydratorInterface;

class EventHydrator implements HydratorInterface {
    public static function HydrateInfo() {
        return [
            'eventCategory' => Util::Entity(function (Event $e) {
                return $e->getEventCategory();
            }),
            'eventInstances' => Util::Collection(function (Event $e) {
                return new EventInstanceCollection($e->getEventInstances());
            }, null),
        ];
    }

    /**
     * @param object $object
     *
     * @return array
     */
    public function extract($object) {
        /** @var Event $event */
        $event = $object;

        return [
            'id' => $event->getId(),
            'title' => $event->getTitle(),

            'camp' => new EntityLink($event->getCamp()),
            'eventCategory' => EntityLink::Create($event->getEventCategory()),

            'eventInstances' => new EntityLinkCollection($event->getEventInstances()),

            'eventPlugins' => new EntityLinkCollection($event->getEventPlugins()),

            //            'eventPlugins' => Link::factory([
            //                'rel' => 'eventPlugins',
            //                'route' => [
            //                    'name' => 'e-camp-api.rest.doctrine.event-plugin',
            //                    'options' => [ 'query' => [ 'eventId' => $event->getId() ] ]
            //                ]
            //            ]),
        ];
    }

    /**
     * @param object $object
     *
     * @return object
     */
    public function hydrate(array $data, $object) {
        /** @var Event $event */
        $event = $object;

        if (isset($data['title'])) {
            $event->setTitle($data['title']);
        }

        return $event;
    }
}
