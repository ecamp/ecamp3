<?php

namespace eCamp\ContentType\Textarea\Hydrator;

use eCamp\ContentType\Textarea\Entity\Textarea;
use Laminas\ApiTools\Hal\Link\Link;
use Laminas\Hydrator\HydratorInterface;

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
        /** @var Textarea $textarea */
        $textarea = $object;

        if (isset($data['text'])) {
            $textarea->setText($data['text']);
        }

        // @var Textarea $textarea
        return $object;
    }
}
