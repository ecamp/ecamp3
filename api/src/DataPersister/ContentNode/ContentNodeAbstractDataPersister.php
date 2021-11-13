<?php

namespace App\DataPersister\ContentNode;

use App\DataPersister\Util\AbstractDataPersister;
use App\Entity\ContentNode;

abstract class ContentNodeAbstractDataPersister extends AbstractDataPersister {
    /**
     * @template T of ContentNode
     *
     * @param T $data
     *
     * @return T
     */
    public function beforeCreateContentNode($data) {
        $data->root = $data->parent->root;
        $data->root->addRootDescendant($data);

        // TODO: Check if it's actually allowed to read/copy from this prototype (user access check)
        if (isset($data->prototype)) {
            if (!($data->prototype instanceof ContentNode)) {
                throw new \Exception('Prototype must be of type ContentNode');
            }

            /** @var ContentNode $prototype */
            $prototype = $data->prototype;

            if (!isset($data->contentType)) {
                $data->contentType = $prototype->contentType;
            }
            if (!isset($data->instanceName)) {
                $data->instanceName = $prototype->instanceName;
            }
            if (!isset($data->slot)) {
                $data->slot = $prototype->slot;
            }
            if (!isset($data->position)) {
                $data->position = $prototype->position;
            }
        }

        return $data;
    }
}
