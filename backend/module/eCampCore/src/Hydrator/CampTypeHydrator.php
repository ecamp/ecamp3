<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\CampType;
use eCamp\Core\Entity\Period;
use eCamp\Lib\Entity\EntityLink;
use eCamp\Lib\Entity\EntityLinkCollection;
use eCamp\Lib\Hydrator\Util;
use eCampApi\V1\Rest\Day\DayCollection;
use eCampApi\V1\Rest\EventInstance\EventInstanceCollection;
use eCampApi\V1\Rest\EventType\EventTypeCollection;
use Zend\Hydrator\HydratorInterface;
use ZF\Hal\Link\Link;

class CampTypeHydrator implements HydratorInterface {
    public static function HydrateInfo() {
        return [
        ];
    }

    /**
     * @param object $object
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
     * @param array $data
     * @param object $object
     * @return object
     */
    public function hydrate(array $data, $object) {
        /** @var CampType $campType */
        $campType = $object;

        $campType->setName($data['name']);
        $campType->setIsJS($data['is_js']);
        $campType->setIsCourse($data['is_course']);
        $campType->setOrganization($data['organization']);

        return $campType;
    }
}
