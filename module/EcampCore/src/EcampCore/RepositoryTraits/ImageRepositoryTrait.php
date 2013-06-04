<?php

namespace EcampCore\RepositoryTraits;

trait ImageRepositoryTrait
{
    /**
     * @var Doctrine\ORM\EntityRepository
     */
    private $imageRepository;

    /**
     * @return Doctrine\ORM\EntityRepository
     */
    public function getImageRepository()
    {
        return $this->imageRepository;
    }

    public function setImageRepository($imageRepository)
    {
        $this->imageRepository = $imageRepository;

        return $this;
    }
}
