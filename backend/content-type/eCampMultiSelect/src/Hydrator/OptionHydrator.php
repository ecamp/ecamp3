<?php

namespace eCamp\ContentType\MultiSelect\Hydrator;

use eCamp\ContentType\MultiSelect\Entity\Option;
use Laminas\ApiTools\Hal\Link\Link;
use Laminas\Hydrator\HydratorInterface;

class OptionHydrator implements HydratorInterface {
    /**
     * @param object $object
     *
     * @return array
     */
    public function extract($object) {
        /** @var Option $option */
        $option = $object;

        return [
            'id' => $option->getId(),
            'pos' => $option->getPos(),
            'key' => $option->getKey(),
            'checked' => $option->getChecked(),

            'activityContent' => Link::factory([
                'rel' => 'activityContent',
                'route' => [
                    'name' => 'e-camp-api.rest.doctrine.activity-content',
                    'params' => ['activityContentId' => $option->getActivityContent()->getId()],
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
        /** @var Option $option */
        $option = $object;

        if (isset($data['pos'])) {
            $option->setPos($data['pos']);
        }

        if (isset($data['key'])) {
            $option->setKey($data['key']);
        }

        if (isset($data['checked'])) {
            $option->setChecked($data['checked']);
        }

        return $option;
    }
}
