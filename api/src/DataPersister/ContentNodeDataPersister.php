<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\ContentNode;
use Symfony\Component\Security\Core\Security;

class ContentNodeDataPersister implements ContextAwareDataPersisterInterface {
    private ContextAwareDataPersisterInterface $dataPersister;
    private Security $security;

    public function __construct(ContextAwareDataPersisterInterface $dataPersister, Security $security) {
        $this->dataPersister = $dataPersister;
        $this->security = $security;
    }

    public function supports($data, array $context = []): bool {
        return ($data instanceof ContentNode) && $this->dataPersister->supports($data, $context);
    }

    /**
     * @param ContentNode $data
     *
     * @return object|void
     */
    public function persist($data, array $context = []) {
        if ('post' === ($context['collection_operation_name'] ?? null)) {
            $data->root = $data->parent->root;
        }

        return $this->dataPersister->persist($data, $context);
    }

    public function remove($data, array $context = []) {
        return $this->dataPersister->remove($data, $context);
    }
}
