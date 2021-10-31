<?php

namespace App\DataPersister\ContentNode;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\ContentNode;

class ContentNodeBaseDataPersister {
    public function __construct(protected ContextAwareDataPersisterInterface $dataPersister) {
    }

    public function remove($data, array $context = []) {
        return $this->dataPersister->remove($data, $context);
    }

    /**
     * @param ContentNode $data
     *
     * @return object|void
     */
    public function persist($data, array $context = []) {
        if (
                'post' === ($context['collection_operation_name'] ?? null)
                || 'create' === ($context['graphql_operation_name'] ?? null)
            ) {
            $this->onCreate($data);
        }

        return $this->dataPersister->persist($data, $context);
    }

    /**
     * @param ContentNode $data
     */
    public function onCreate($data) {
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
    }
}
