<?php

namespace EcampCore\RepositoryTraits;

trait CampRepositoryTrait
{
    /**
     * @var EcampCore\Repository\CampRepository
     */
    private $campRepository;

    /**
     * @return EcampCore\Repository\CampRepository
     */
    public function getCampRepository()
    {
        return $this->campRepository;
    }

    public function setCampRepository($campRepository)
    {
        $this->campRepository = $campRepository;

        return $this;
    }
}
