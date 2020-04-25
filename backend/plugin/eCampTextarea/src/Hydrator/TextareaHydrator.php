<?php

namespace eCamp\Plugin\Textarea\Hydrator;

use eCamp\Plugin\Textarea\Entity\Textarea;
use Zend\Hydrator\HydratorInterface;

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
            'event_plugin' => $textarea->getEventPlugin(),
        ];
    }

    /**
     * @param object $object
     *
     * @return object
     */
    public function hydrate(array $data, $object) {
        /** @var Textarea $textarea */
        return $object;
    }
}
