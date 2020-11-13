<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\MaterialList;
use eCamp\Lib\Entity\EntityLink;
use Laminas\ApiTools\Hal\Link\Link;
use Laminas\Hydrator\HydratorInterface;

class MaterialListHydrator implements HydratorInterface {
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
        /** @var MaterialList $materialList */
        $materialList = $object;

        return [
            'id' => $materialList->getId(),
            'name' => $materialList->getName(),
            'camp' => EntityLink::Create($materialList->getCamp()),
            
            'materialItems' => Link::factory([
                'rel' => 'materialItems',
                'route' => [
                    'name' => 'e-camp-api.rest.doctrine.material-item',
                    'options' => ['query' => ['materialListId' => $materialList->getId()]],
                ]
            ])
        ];
    }

    /**
     * @param object $object
     *
     * @return object
     */
    public function hydrate(array $data, $object) {
        /** @var MaterialList $materialList */
        $materialList = $object;

        if (isset($data['name'])) {
            $materialList->setName($data['name']);
        }

        return $materialList;
    }
}
