<?php

namespace EcampCore\RepositoryTraits;

trait PluginPositionRepositoryTrait
{
    /**
     * @var Doctrine\ORM\EntityRepository
     */
    private $pluginPositionRepository;

    /**
     * @return Doctrine\ORM\EntityRepository
     */
    public function getPluginPositionRepository()
    {
        return $this->pluginPositionRepository;
    }

    public function setPluginPositionRepository($pluginPositionRepository)
    {
        $this->pluginPositionRepository = $pluginPositionRepository;

        return $this;
    }
}
