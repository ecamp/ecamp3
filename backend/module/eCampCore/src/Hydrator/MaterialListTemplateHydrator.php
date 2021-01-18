<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\MaterialListTemplate;
use eCamp\Lib\Entity\EntityLink;
use Laminas\Hydrator\HydratorInterface;

class MaterialListTemplateHydrator implements HydratorInterface {
    public static function HydrateInfo() {
        return [
        ];
    }

    /**
     * @param object $object
     *
     * @return array
     */
    public function extract($object) {
        /** @var MaterialListTemplate $materialListTemplate */
        $materialListTemplate = $object;

        return [
            'id' => $materialListTemplate->getId(),
            'name' => $materialListTemplate->getName(),
            'campTemplate' => EntityLink::Create($materialListTemplate->getCampTemplate()),
        ];
    }

    /**
     * @param object $object
     *
     * @return object
     */
    public function hydrate(array $data, $object) {
        /** @var MaterialListTemplate $materialListTemplate */
        $materialListTemplate = $object;

        if (isset($data['name'])) {
            $materialListTemplate->setName($data['name']);
        }

        return $materialListTemplate;
    }
}
