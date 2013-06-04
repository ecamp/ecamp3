<?php

namespace EcampCore\RepositoryTraits;

trait CampTypeRepositoryTrait
{
    /**
     * @var Doctrine\ORM\EntityRepository
     */
    private $campTypeRepository;

    /**
     * @return Doctrine\ORM\EntityRepository
     */
    public function getCampTypeRepository()
    {
        return $this->campTypeRepository;
    }

    public function setCampTypeRepository($campTypeRepository)
    {
        $this->campTypeRepository = $campTypeRepository;

        return $this;
    }
}
