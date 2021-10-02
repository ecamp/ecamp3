<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Camp;
use App\Entity\CampCollaboration;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class CampDataPersister implements ContextAwareDataPersisterInterface {
    public function __construct(private ContextAwareDataPersisterInterface $dataPersister, private Security $security, private EntityManagerInterface $em) {
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
        /** @var User $user */
        $user = null;
        if ('post' === ($context['collection_operation_name'] ?? null)) {
            /** @var User $user */
            $user = $this->security->getUser();
            $data->creator = $user;
            $data->owner = $user;

            // TODO prototype cloning logic here? Or in a separate endpoint?
        }

        /** @var Camp $camp */
        $camp = $this->dataPersister->persist($data, $context);

        if ('post' === ($context['collection_operation_name'] ?? null)) {
            $collaboration = new CampCollaboration();
            $collaboration->user = $user;
            $collaboration->camp = $camp;
            $collaboration->role = CampCollaboration::ROLE_MANAGER;
            $collaboration->status = CampCollaboration::STATUS_ESTABLISHED;
            $this->em->persist($collaboration);
            $this->em->flush();
        }

        return $camp;
    }

    public function remove($data, array $context = []) {
        return $this->dataPersister->remove($data, $context);
    }
}
