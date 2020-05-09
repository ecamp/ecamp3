<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\CampType;
use eCamp\Lib\Entity\EntityLink;
use eCamp\Lib\Entity\EntityLinkCollection;
use Zend\Hydrator\HydratorInterface;

class CampTypeHydrator implements HydratorInterface {
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
        /** @var CampType $campType */
        $campType = $object;

        return [
            'id' => $campType->getId(),
            'name' => $campType->getName(),
            'isJS' => $campType->getIsJS(),
            'isCourse' => $campType->getIsCourse(),

            'organization' => EntityLink::Create($campType->getOrganization()),
            'eventTypes' => new EntityLinkCollection($campType->getEventTypes()),
        ];
    }

    /**
     * @param object $object
     *
     * @return object
     */
    public function hydrate(array $data, $object) {
        /** @var CampType $campType */
        $campType = $object;

        if (isset($data['name'])) {
            $campType->setName($data['name']);
        }
        if (isset($data['isJS'])) {
            $campType->setIsJS($data['isJS']);
        }
        if (isset($data['isCourse'])) {
            $campType->setIsCourse($data['isCourse']);
        }
        if (isset($data['organization'])) {
            $campType->setOrganization($data['organization']);
        }

        return $campType;
    }
}
