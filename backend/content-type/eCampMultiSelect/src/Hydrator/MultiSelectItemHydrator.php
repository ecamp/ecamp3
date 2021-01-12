<?php

namespace eCamp\ContentType\MultiSelect\Hydrator;

use eCamp\ContentType\MultiSelect\Entity\MultiSelectItem;
use Laminas\ApiTools\Hal\Link\Link;
use Laminas\Hydrator\HydratorInterface;

class MultiSelectItemHydrator implements HydratorInterface {
    /**
     * @param object $object
     *
     * @return array
     */
    public function extract($object) {
        /** @var MultiSelectItem $multiSelectItem */
        $multiSelectItem = $object;

        return [
            'id' => $multiSelectItem->getId(),
            'pos' => $multiSelectItem->getPos(),
            'key' => $multiSelectItem->getKey(),
            'checked' => $multiSelectItem->getChecked(),

            'activityContent' => Link::factory([
                'rel' => 'activityContent',
                'route' => [
                    'name' => 'e-camp-api.rest.doctrine.activity-content',
                    'params' => ['activityContentId' => $multiSelectItem->getActivityContent()->getId()],
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
        /** @var MultiSelectItem $multiSelectItem */
        $multiSelectItem = $object;

        if (isset($data['pos'])) {
            $multiSelectItem->setPos($data['pos']);
        }

        if (isset($data['key'])) {
            $multiSelectItem->setKey($data['key']);
        }

        if (isset($data['checked'])) {
            $multiSelectItem->setChecked($data['checked']);
        }

        return $multiSelectItem;
    }
}
