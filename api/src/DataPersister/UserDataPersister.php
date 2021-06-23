<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserDataPersister implements ContextAwareDataPersisterInterface {
    private ContextAwareDataPersisterInterface $dataPersister;
    private UserPasswordEncoderInterface $userPasswordEncoder;

    public function __construct(ContextAwareDataPersisterInterface $dataPersister, UserPasswordEncoderInterface $userPasswordEncoder) {
        $this->dataPersister = $dataPersister;
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    public function supports($data, array $context = []): bool {
        return ($data instanceof User) && $this->dataPersister->supports($data, $context);
    }

    public function persist($data, array $context = []) {
        if ($data->plainPassword) {
            $data->password = $this->userPasswordEncoder->encodePassword($data, $data->plainPassword);
            $data->eraseCredentials();
        }

        return $this->dataPersister->persist($data, $context);
    }

    public function remove($data, array $context = []) {
        return $this->dataPersister->remove($data, $context);
    }
}
