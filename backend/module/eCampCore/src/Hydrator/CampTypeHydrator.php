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
            'is_js' => $campType->getIsJS(),
            'is_course' => $campType->getIsCourse(),

            'organization' => EntityLink::Create($campType->getOrganization()),
            'event_types' => new EntityLinkCollection($campType->getEventTypes()),
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

        if( isset($data['name']) ) $campType->setName($data['name']);
        if( isset($data['is_js']) ) $campType->setIsJS($data['is_js']);
        if( isset($data['is_course']) ) $campType->setIsCourse($data['is_course']);
        if( isset($data['organization']) ) $campType->setOrganization($data['organization']);

        return $campType;
    }
}
