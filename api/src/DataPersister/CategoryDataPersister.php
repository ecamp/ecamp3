<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Category;
use App\Entity\ContentNode\ColumnLayout;
use App\Entity\ContentType;
use Doctrine\ORM\EntityManagerInterface;

class CategoryDataPersister implements ContextAwareDataPersisterInterface {
    public function __construct(private ContextAwareDataPersisterInterface $dataPersister, private EntityManagerInterface $entityManager) {
    }

    public function supports($data, array $context = []): bool {
        return ($data instanceof Category) && $this->dataPersister->supports($data, $context);
    }

    /**
     * @param Category $data
     *
     * @return object|void
     */
    public function persist($data, array $context = []) {
        if ('post' === ($context['collection_operation_name'] ?? null)) {
            // TODO implement actual prototype cloning and strategy classes, this is just a dummy implementation to
            //      fill the non-nullable field for Doctrine
            $rootContentNode = new ColumnLayout();
            $rootContentNode->contentType = $this->entityManager
                ->getRepository(ContentType::class)
                ->findOneBy(['name' => 'ColumnLayout'])
            ;

            $data->setRootContentNode($rootContentNode);
        }

        return $this->dataPersister->persist($data, $context);
    }

    public function remove($data, array $context = []) {
        return $this->dataPersister->remove($data, $context);
    }
}
