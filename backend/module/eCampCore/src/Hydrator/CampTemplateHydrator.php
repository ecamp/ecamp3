<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\CampTemplate;
use eCamp\Lib\Entity\EntityLinkCollection;
use Laminas\Hydrator\HydratorInterface;

class CampTemplateHydrator implements HydratorInterface {
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
        /** @var CampTemplate $campTemplate */
        $campTemplate = $object;

        return [
            'id' => $campTemplate->getId(),
            'name' => $campTemplate->getName(),

            'activityCategoryTemplates' => new EntityLinkCollection($campTemplate->getActivityCategoryTemplate()),
        ];
    }

    /**
     * @param object $object
     *
     * @return object
     */
    public function hydrate(array $data, $object) {
        /** @var CampTemplate $campTemplate */
        $campTemplate = $object;

        if (isset($data['name'])) {
            $campTemplate->setName($data['name']);
        }

        return $campTemplate;
    }
}
