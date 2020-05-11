<?php

namespace eCamp\ContentType\Textarea\Hydrator;

use eCamp\ContentType\Textarea\Entity\Textarea;
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

            'activityContent' => Link::factory([
                'rel' => 'activityContent',
                'route' => [
                    'name' => 'e-camp-api.rest.doctrine.activity-content',
                    'params' => ['activityContentId' => $textarea->getActivityContent()->getId()],
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
