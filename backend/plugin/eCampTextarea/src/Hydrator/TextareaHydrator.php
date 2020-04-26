<?php

namespace eCamp\Plugin\Textarea\Hydrator;

use ZF\Hal\Link\Link;
use eCamp\Lib\Entity\EntityLink;
use Zend\Hydrator\HydratorInterface;
use eCamp\Plugin\Textarea\Entity\Textarea;

class TextareaHydrator implements HydratorInterface {
    /**
     * @param object $object
     * @return array
     */
    public function extract($object) {
        /** @var Textarea $textarea */
        $textarea = $object;

        return [
            'id' => $textarea->getId(),
            'text' => $textarea->getText(),

            'event_plugin' => Link::factory([
                'rel' => 'event_plugin',
                'route' => [
                    'name' => 'e-camp-api.rest.doctrine.event-plugin',
                    'params' => [ 'event_plugin_id' => $textarea->getEventPlugin()->getId() ]
                ]
            ]),
        ];
    }

    /**
     * @param array $data
     * @param object $object
     * @return object
     */
    public function hydrate(array $data, $object) {
        /** @var Textarea $textarea */
        $textarea = $object;

        return $textarea;
    }
}
