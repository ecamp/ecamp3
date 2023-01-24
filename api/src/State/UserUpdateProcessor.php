<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use App\State\Util\AbstractPersistProcessor;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserUpdateProcessor extends AbstractPersistProcessor {
    public function __construct(
        ProcessorInterface $decorated,
        private UserPasswordHasherInterface $userPasswordHasher
    ) {
        parent::__construct($decorated);
    }

    /**
     * @param User $data
     */
    public function onBefore($data, Operation $operation, array $uriVariables = [], array $context = []): User {
        if ($data->plainPassword) {
            $data->password = $this->userPasswordHasher->hashPassword($data, $data->plainPassword);
            $data->eraseCredentials();
        }

        return $data;
    }
}
