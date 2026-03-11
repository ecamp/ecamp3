<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use App\State\Util\AbstractPersistProcessor;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @template-extends AbstractPersistProcessor<User>
 */
class UserUpdateProcessor extends AbstractPersistProcessor {
    public function __construct(
        ProcessorInterface $decorated,
        private readonly UserPasswordHasherInterface $userPasswordHasher
    ) {
        parent::__construct($decorated);
    }

    /**
     * @param User $data
     */
    #[\Override]
    public function onBefore($data, Operation $operation, array $uriVariables = [], array $context = []): User {
        if ($data->plainPassword) {
            $data->password = $this->userPasswordHasher->hashPassword($data, $data->plainPassword);
            $data->prepareForSerialization();
        }

        return $data;
    }
}
