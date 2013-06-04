<?php

namespace EcampCore\RepositoryTraits;

trait PluginRepositoryTrait
{
    /**
     * @var Doctrine\ORM\EntityRepository
     */
    private $pluginRepository;

    /**
     * @return Doctrine\ORM\EntityRepository
     */
    public function getPluginRepository()
    {
        return $this->pluginRepository;
    }

    public function setPluginRepository($pluginRepository)
    {
        $this->pluginRepository = $pluginRepository;

        return $this;
    }
}
