<?php

namespace eCamp\ContentType\TextList\Hydrator;

use eCamp\ContentType\SingleText\Entity\TextListItem;
use Laminas\ApiTools\Hal\Link\Link;
use Laminas\Hydrator\HydratorInterface;

class TextListItemHydrator implements HydratorInterface {
    /**
     * @param object $object
     *
     * @return array
     */
    public function extract($object) {
        /** @var TextListItem $listItem */
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
        /** @var TextListItem $listItem */
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
