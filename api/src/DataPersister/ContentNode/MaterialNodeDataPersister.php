<?php

namespace App\DataPersister\ContentNode;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\ContentNode\MaterialNode;
use App\Entity\MaterialItem;

class MaterialNodeDataPersister extends ContentNodeBaseDataPersister implements ContextAwareDataPersisterInterface {
    public function supports($materialNode, array $context = []): bool {
        return ($materialNode instanceof MaterialNode) && $this->dataPersister->supports($materialNode, $context);
    }

    /**
     * @param MaterialNode $materialNode
     */
    public function onCreate($materialNode) {
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

        parent::onCreate($materialNode);
    }
}
