<?php

namespace eCamp\Plugin\Textarea\Hydrator;

use eCamp\Plugin\Textarea\Entity\Textarea;
use Zend\Hydrator\HydratorInterface;
use ZF\Hal\Link\Link;

class TextareaHydrator implements HydratorInterface {
    /**
     * @param object $object
     *
     * @return array
     */
    public function extract($object) {
        /** @var Textarea $textarea */
        $textarea = $object;

        return [
            'id' => $textarea->getId(),
            'text' => $textarea->getText(),

            'eventPlugin' => Link::factory([
                'rel' => 'eventPlugin',
                'route' => [
                    'name' => 'e-camp-api.rest.doctrine.event-plugin',
                    'params' => ['eventPluginId' => $textarea->getEventPlugin()->getId()],
                ],
            ]),
        ];
    }

    /**
     * @param object $object
     *
     * @return object
     */
    public function hydrate(array $data, $object) {
        // @var Textarea $textarea
        return $object;
    }
}
