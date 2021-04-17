<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\MaterialItem;
use eCamp\Lib\Entity\EntityLink;
use Laminas\Hydrator\HydratorInterface;

class MaterialItemHydrator implements HydratorInterface {
    public static function HydrateInfo(): array {
        return [
        ];
    }

    /**
     * @param object $object
     */
    public function extract($object): array {
        /** @var MaterialItem $materialItem */
        $materialItem = $object;

        return [
            'id' => $materialItem->getId(),
            'article' => $materialItem->getArticle(),
            'quantity' => $materialItem->getQuantity(),
            'unit' => $materialItem->getUnit(),

            'materialList' => EntityLink::Create($materialItem->getMaterialList()),
            'period' => EntityLink::Create($materialItem->getPeriod()),
            'contentNode' => EntityLink::Create($materialItem->getContentNode()),
        ];
    }

    /**
     * @param object $object
     */
    public function hydrate(array $data, $object): MaterialItem {
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
