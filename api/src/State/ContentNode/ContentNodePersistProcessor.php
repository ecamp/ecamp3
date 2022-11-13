<?php

namespace App\State\ContentNode;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Post;
use App\Entity\ContentNode;
use App\State\Util\AbstractPersistProcessor;

class ContentNodePersistProcessor extends AbstractPersistProcessor {
    /**
     * @template T of ContentNode
     *
     * @param T $data
     *
     * @return T
     */
    public function onBefore($data, Operation $operation, array $uriVariables = [], array $context = []): ContentNode {
        $data = parent::onBefore($data, $operation, $uriVariables, $context);

        if ($operation instanceof Post) {
            // set root from parent
            $data->parent->addChild($data);
            $data->parent->root->addRootDescendant($data);
        }

        return $data;
    }
}
