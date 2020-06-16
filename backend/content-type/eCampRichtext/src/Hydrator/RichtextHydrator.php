<?php

namespace eCamp\ContentType\Richtext\Hydrator;

use eCamp\ContentType\Richtext\Entity\Richtext;
use Laminas\ApiTools\Hal\Link\Link;
use Laminas\Hydrator\HydratorInterface;

class RichtextHydrator implements HydratorInterface {
    /**
     * @param object $object
     *
     * @return array
     */
    public function extract($object) {
        /** @var Richtext $richtext */
        $richtext = $object;

        return [
            'id' => $richtext->getId(),
            'text' => $richtext->getText(),

            'activityContent' => Link::factory([
                'rel' => 'activityContent',
                'route' => [
                    'name' => 'e-camp-api.rest.doctrine.activity-content',
                    'params' => ['activityContentId' => $richtext->getActivityContent()->getId()],
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
        /** @var Richtext $richtext */
        $richtext = $object;

        if (isset($data['text'])) {
            $richtext->setText($data['text']);
        }

        return $richtext;
    }
}
