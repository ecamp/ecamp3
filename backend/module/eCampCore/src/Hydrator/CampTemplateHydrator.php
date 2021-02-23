<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\CampTemplate;
use eCamp\Lib\Entity\EntityLinkCollection;
use Laminas\Hydrator\HydratorInterface;

class CampTemplateHydrator implements HydratorInterface {
    public static function HydrateInfo(): array {
        return [
        ];
    }

    /**
     * @param object $object
     */
    public function extract($object): array {
        /** @var CampTemplate $campTemplate */
        $campTemplate = $object;

        return [
            'id' => $campTemplate->getId(),
            'name' => $campTemplate->getName(),

            'categoryTemplates' => new EntityLinkCollection($campTemplate->getCategoryTemplates()),
        ];
    }

    /**
     * @param object $object
     */
    public function hydrate(array $data, $object): CampTemplate {
        /** @var CampTemplate $campTemplate */
        $campTemplate = $object;

        if (isset($data['name'])) {
            $campTemplate->setName($data['name']);
        }

        return $campTemplate;
    }
}
