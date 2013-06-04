<?php

namespace EcampCore\RepositoryTraits;

trait LoginRepositoryTrait
{
    /**
     * @var EcampCore\Repository\LoginRepository
     */
    private $loginRepository;

    /**
     * @return EcampCore\Repository\LoginRepository
     */
    public function getLoginRepository()
    {
        return $this->loginRepository;
    }

    public function setLoginRepository($loginRepository)
    {
        $this->loginRepository = $loginRepository;

        return $this;
    }
}
