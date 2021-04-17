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

            'contentNode' => Link::factory([
                'rel' => 'contentNode',
                'route' => [
                    'name' => 'e-camp-api.rest.doctrine.content-node',
                    'params' => ['contentNodeId' => $singleText->getContentNode()->getId()],
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
