<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserDataPersister implements ContextAwareDataPersisterInterface {
    private ContextAwareDataPersisterInterface $decorated;
    private UserPasswordEncoderInterface $userPasswordEncoder;

    public function __construct(ContextAwareDataPersisterInterface $decorated, UserPasswordEncoderInterface $userPasswordEncoder) {
        $this->decorated = $decorated;
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    public function supports($data, array $context = []): bool {
        return ($data instanceof User) && $this->decorated->supports($data, $context);
    }

    public function persist($data, array $context = []) {
        if ($data->getPlainPassword()) {
            $data->setPassword(
                $this->userPasswordEncoder->encodePassword($data, $data->getPlainPassword())
            );
            $data->eraseCredentials();
        }

        return $this->decorated->persist($data, $context);
    }

    public function remove($data, array $context = []) {
        return $this->decorated->remove($data, $context);
    }
}
