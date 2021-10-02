<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\User;
use App\Service\MailService;
use App\Util\IdGenerator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserDataPersister implements ContextAwareDataPersisterInterface {
    public function __construct(
        private ContextAwareDataPersisterInterface $dataPersister,
        private UserPasswordHasherInterface $userPasswordHasher,
        private MailService $mailService
    ) {
    }

    public function supports($data, array $context = []): bool {
        return ($data instanceof User) && $this->dataPersister->supports($data, $context);
    }

    public function persist($data, array $context = []) {
        if ('post' === ($context['collection_operation_name'] ?? null)) {
            $data->state = User::STATE_REGISTERED;

            $activationKey = IdGenerator::generateRandomHexString(64);
            $data->activationKeyHash = md5($activationKey);

        // TODO
            // Send Activation-Mail with $activationKey
            // $this->mailService->
        } elseif (User::ACTIVATE === ($context['item_operation_name'] ?? null)) {
            if ($data->activationKeyHash === md5($data->activationKey)) {
                $data->state = User::STATE_ACTIVATED;
                $data->activationKey = null;
                $data->activationKeyHash = null;
            }
        }

        if ($data->plainPassword) {
            $data->password = $this->userPasswordHasher->hashPassword($data, $data->plainPassword);
            $data->eraseCredentials();
        }

        return $this->dataPersister->persist($data, $context);
    }

    public function remove($data, array $context = []) {
        return $this->dataPersister->remove($data, $context);
    }
}
