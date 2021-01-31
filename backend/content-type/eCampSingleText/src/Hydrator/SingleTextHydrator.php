<?php

namespace eCamp\ContentType\SingleText\Hydrator;

use eCamp\ContentType\SingleText\Entity\SingleText;
use Laminas\ApiTools\Hal\Link\Link;
use Laminas\Hydrator\HydratorInterface;

class SingleTextHydrator implements HydratorInterface {
    /**
     * @param object $object
     */
    public function extract($object): array {
        /** @var SingleText $singleText */
        $singleText = $object;

        return [
            'id' => $singleText->getId(),
            'text' => $singleText->getText(),

            'activityContent' => Link::factory([
                'rel' => 'activityContent',
                'route' => [
                    'name' => 'e-camp-api.rest.doctrine.activity-content',
                    'params' => ['activityContentId' => $singleText->getActivityContent()->getId()],
                ],
            ]),
        ];
    }

    /**
     * @param object $object
     */
    public function hydrate(array $data, $object): SingleText {
        /** @var SingleText $singleText */
        $singleText = $object;

        if (isset($data['text'])) {
            $singleText->setText($data['text']);
        }

        return $singleText;
    }
}
