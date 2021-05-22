<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Camp;
use App\Entity\User;
use Symfony\Component\Security\Core\Security;

class CampDataPersister implements ContextAwareDataPersisterInterface {
    private ContextAwareDataPersisterInterface $dataPersister;
    private Security $security;

    public function __construct(ContextAwareDataPersisterInterface $dataPersister, Security $security) {
        $this->dataPersister = $dataPersister;
        $this->security = $security;
    }

    public function supports($data, array $context = []): bool {
        return ($data instanceof Camp) && $this->dataPersister->supports($data, $context);
    }

    /**
     * @param Camp $data
     *
     * @return object|void
     */
    public function persist($data, array $context = []) {
        if ('post' === ($context['collection_operation_name'] ?? null)) {
            /** @var User $user */
            $user = $this->security->getUser();
            $data->setCreator($user);
            $data->setOwner($user);

            // TODO prototype cloning logic here? Or in a separate endpoint?
            $data->setIsPrototype(false);
        }

        return $this->dataPersister->persist($data, $context);
    }

    public function remove($data, array $context = []) {
        return $this->dataPersister->remove($data, $context);
    }
}
