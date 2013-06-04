<?php

namespace EcampCore\RepositoryTraits;

trait UserRepositoryTrait
{
    /**
     * @var EcampCore\Repository\UserRepository
     */
    private $userRepository;

    /**
     * @return EcampCore\Repository\UserRepository
     */
    public function getUserRepository()
    {
        return $this->userRepository;
    }

    public function setUserRepository($userRepository)
    {
        $this->userRepository = $userRepository;

        return $this;
    }
}
