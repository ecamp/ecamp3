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
    public function beforeCreate($data) {
        $data = parent::beforeCreate($data);

        // set root from parent
        $data->parent->addChild($data);
        $data->parent->root->addRootDescendant($data);

        return $data;
    }
}
