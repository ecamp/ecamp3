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
        if ($data->plainPassword) {
            $data->password = $this->userPasswordHasher->hashPassword($data, $data->plainPassword);
            $data->eraseCredentials();
        }

        $data->state = User::STATE_REGISTERED;

        $activationKey = IdGenerator::generateRandomHexString(64);
        $data->activationKeyHash = md5($activationKey);

        // TODO: Send Activation-Mail
        // $this->mailService->

        return $this->dataPersister->persist($data, $context);
    }

    public function remove($data, array $context = []) {
        return $this->dataPersister->remove($data, $context);
    }
}
