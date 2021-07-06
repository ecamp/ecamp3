<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Activity;
use Symfony\Component\Security\Core\Security;

class ActivityDataPersister implements ContextAwareDataPersisterInterface {
    private ContextAwareDataPersisterInterface $dataPersister;
    private Security $security;

    public function __construct(ContextAwareDataPersisterInterface $dataPersister, Security $security) {
        $this->dataPersister = $dataPersister;
        $this->security = $security;
    }

    public function supports($data, array $context = []): bool {
        return ($data instanceof Activity) && $this->dataPersister->supports($data, $context);
    }

    /**
     * @param Activity $data
     *
     * @return object|void
     */
    public function persist($data, array $context = []) {
        $data->camp = $data->category->camp;

        if ('post' === ($context['collection_operation_name'] ?? null)) {
            // TODO implement actual prototype cloning, this is just here to fill the non-nullable field for Doctrine
            $data->setRootContentNode($data->category->rootContentNode);
        }

        return $this->dataPersister->persist($data, $context);
    }

    public function remove($data, array $context = []) {
        return $this->dataPersister->remove($data, $context);
    }
}
