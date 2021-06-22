<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\ContentNodeStrategy\ContentNodeStrategy;
use App\Entity\ContentNode;
use Symfony\Component\DependencyInjection\ServiceLocator;

class ContentNodeDataPersister implements ContextAwareDataPersisterInterface {
    private ContextAwareDataPersisterInterface $dataPersister;
    private ServiceLocator $strategyServiceLocator;

    public function __construct(ContextAwareDataPersisterInterface $dataPersister, ServiceLocator $strategyServiceLocator) {
        $this->dataPersister = $dataPersister;
        $this->strategyServiceLocator = $strategyServiceLocator;
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
            /** @var ContentNodeStrategy $strategy */
            $strategy = $this->strategyServiceLocator->get($data->getContentType()->getStrategyClass());
            $strategy->contentNodeCreated($data, $data->getPrototype());
        }

        $this->dataPersister->persist($data, $context);

        return $data;
    }

    public function remove($data, array $context = []) {
        return $this->dataPersister->remove($data, $context);
    }
}
