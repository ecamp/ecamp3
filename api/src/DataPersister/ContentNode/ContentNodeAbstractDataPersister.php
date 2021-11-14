<?php

namespace App\DataPersister\ContentNode;

use App\DataPersister\Util\AbstractDataPersister;
use App\Entity\ContentNode;

abstract class ContentNodeAbstractDataPersister extends AbstractDataPersister {
    /**
     * @param ContentNode $data
     */
    public function beforeCreate($data): ContentNode {
        $data->root = $data->parent->root;
        $data->root->addRootDescendant($data);

        // TODO: Check if it's actually allowed to read/copy from this prototype (user access check)
        if (isset($data->prototype)) {
            if (!is_a($data->prototype, $data::class)) {
                throw new \Exception('Prototype must be of type '.$data::class);
            }

            // deep copy from prototype
            $data->copyFromPrototype($data->prototype);
        }

        return $data;
    }
}
