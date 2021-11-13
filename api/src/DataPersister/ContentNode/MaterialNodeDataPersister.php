<?php

namespace App\DataPersister\ContentNode;

use App\DataPersister\Util\DataPersisterObservable;
use App\Entity\ContentNode\MaterialNode;
use App\Entity\MaterialItem;

class MaterialNodeDataPersister extends ContentNodeAbstractDataPersister {
    /**
     * @throws \ReflectionException
     */
    public function __construct(
        DataPersisterObservable $dataPersisterObservable
    ) {
        parent::__construct(
            MaterialNode::class,
            $dataPersisterObservable
        );
    }

    public function beforeCreate($data): MaterialNode {
        /** @var MaterialNode $materialNode */
        $materialNode = $data;

        if (isset($materialNode->prototype)) {
            if (!($materialNode->prototype instanceof MaterialNode)) {
                throw new \Exception('Prototype must be of type MaterialNode');
            }

            /** @var MaterialNode $prototype */
            $prototype = $materialNode->prototype;

            // copy all material items
            foreach ($prototype->materialItems as $prototypeItem) {
                $materialItem = new MaterialItem();

                $materialItem->article = $prototypeItem->article;
                $materialItem->quantity = $prototypeItem->quantity;
                $materialItem->unit = $prototypeItem->unit;
                $materialItem->materialList = $prototypeItem->materialList;

                $materialNode->addMaterialItem($materialItem);
            }
        }

        return parent::beforeCreateContentNode($materialNode);
    }
}
