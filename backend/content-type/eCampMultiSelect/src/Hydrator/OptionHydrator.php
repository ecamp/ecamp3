<?php

namespace eCamp\ContentType\MultiSelect\Hydrator;

use eCamp\ContentType\MultiSelect\Entity\Option;
use Laminas\ApiTools\Hal\Link\Link;
use Laminas\Hydrator\HydratorInterface;

class OptionHydrator implements HydratorInterface {
    /**
     * @param object $object
     */
    public function extract($object): array {
        /** @var Option $option */
        $option = $object;

        return [
            'id' => $option->getId(),
            'pos' => $option->getPos(),
            'translateKey' => $option->getTranslateKey(),
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
     */
    public function hydrate(array $data, $object): object {
        /** @var Option $option */
        $option = $object;

        if (isset($data['pos'])) {
            $option->setPos($data['pos']);
        }

        if (isset($data['translateKey'])) {
            $option->setTranslateKey($data['translateKey']);
        }

        if (isset($data['checked'])) {
            $option->setChecked($data['checked']);
        }

        return $option;
    }
}
