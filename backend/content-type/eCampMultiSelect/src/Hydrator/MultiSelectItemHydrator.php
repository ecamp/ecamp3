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
        /** @var MultiSelectItem $listItem */
        $listItem = $object;

        return [
            'id' => $listItem->getId(),
            'pos' => $listItem->getPos(),
            'title' => $listItem->getTitle(),
            'description' => $listItem->getDescription(),
            'checked' => $listItem->getChecked(),
            'translated' => $listItem->getTranslated(),

            'activityContent' => Link::factory([
                'rel' => 'activityContent',
                'route' => [
                    'name' => 'e-camp-api.rest.doctrine.activity-content',
                    'params' => ['activityContentId' => $listItem->getActivityContent()->getId()],
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
        /** @var MultiSelectItem $listItem */
        $listItem = $object;

        if (isset($data['pos'])) {
            $listItem->setPos($data['pos']);
        }

        if (isset($data['title'])) {
            $listItem->setTitle($data['title']);
        }

        if (isset($data['description'])) {
            $listItem->setDescription($data['description']);
        }

        if (isset($data['translated'])) {
            $listItem->setTranslated($data['translated']);
        }

        if (isset($data['checked'])) {
            $listItem->setChecked($data['checked']);
        }

        return $listItem;
    }
}
