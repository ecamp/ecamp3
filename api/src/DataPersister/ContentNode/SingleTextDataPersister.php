<?php

namespace App\DataPersister\ContentNode;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\ContentType;
use App\Entity\ContentNode\SingleText;
use App\Entity\ContentNode;
use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;

class SingleTextDataPersister implements ContextAwareDataPersisterInterface {
    public function __construct(private ContextAwareDataPersisterInterface $dataPersister, private EntityManagerInterface $entityManager) {
    }

    public function supports($data, array $context = []): bool {
        return ($data instanceof SingleText) && $this->dataPersister->supports($data, $context);
    }

    /**
     * @param SingleText $data
     *
     * @return object|void
     */
    public function persist($data, array $context = []) {
        if (
                'post' === ($context['collection_operation_name'] ?? null) ||
                'create' === ($context['graphql_operation_name'] ?? null) 
            ) {

            $data->root = $data->parent->root;

            // TODO: Check if it's actually allowed to read/copy from this prototype (user access check)
            if (isset($data->prototype) && $data->prototype instanceof SingleText) {

                /** @var SingleText $prototype */
                $prototype = $data->prototype;

                $data->text = $prototype->text;
            }
        }

        return $this->dataPersister->persist($data, $context);
    }

    public function remove($data, array $context = []) {
        return $this->dataPersister->remove($data, $context);
    }
}
