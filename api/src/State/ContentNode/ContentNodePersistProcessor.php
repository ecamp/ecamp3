<?php

namespace App\State\ContentNode;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Post;
use App\Entity\ContentNode;
use App\State\Util\AbstractPersistProcessor;

/**
 * @template T of ContentNode
 *
 * @template-extends AbstractPersistProcessor<T>
 */
class ContentNodePersistProcessor extends AbstractPersistProcessor {
    /**
     * @param T $data
     */
    public function onBefore($data, Operation $operation, array $uriVariables = [], array $context = []): ContentNode {
        /** @var ContentNode $data */
        $data = parent::onBefore($data, $operation, $uriVariables, $context);

        if ($operation instanceof Post) {
            // set root from parent
            $data->parent->addChild($data);
            $data->parent->root->addRootDescendant($data);
        }

        return $data;
    }
}
