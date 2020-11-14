<?php

namespace eCamp\Core\Hydrator;

use eCamp\Lib\Entity\EntityLink;
use Laminas\Hydrator\HydratorInterface;

class MaterialItemHydrator implements HydratorInterface {
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
        /** @var MaterialItem $materialItem */
        $materialItem = $object;

        return [
            'id' => $materialItem->getId(),
            'article' => $materialItem->getArticle(),
            'quantity' => $materialItem->getQuantity(),
            'unit' => $materialItem->getUnit(),

            'materialList' => EntityLink::Create($materialItem->getMaterialList()),
            'period' => EntityLink::Create($materialItem->getPeriod()),
            'activityContent' => EntityLink::Create($materialItem->getActivityContent()),
        ];
    }

    /**
     * @param object $object
     *
     * @return object
     */
    public function hydrate(array $data, $object) {
        /** @var MaterialItem $materialItem */
        $materialItem = $object;

        if (isset($data['article'])) {
            $materialItem->setArticle($data['article']);
        }
        if (isset($data['quantity'])) {
            $materialItem->setQuantity($data['quantity']);
        }
        if (isset($data['unit'])) {
            $materialItem->setUnit($data['unit']);
        }

        return $materialItem;
    }
}
