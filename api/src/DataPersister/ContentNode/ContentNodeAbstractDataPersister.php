<?php

namespace App\DataPersister\ContentNode;

use App\DataPersister\Util\AbstractDataPersister;
use App\Entity\ContentNode;

abstract class ContentNodeAbstractDataPersister extends AbstractDataPersister {
    /**
     * @param ContentNode $data
     */
    public function beforeCreate($data): ContentNode {
        // set root from parent
        $data->root = $data->parent->root;
        $data->root->addRootDescendant($data);

        return $data;
    }
}
