<?php

namespace EcampCore\RepositoryTraits;

trait PluginInstanceRepositoryTrait
{
    /**
     * @var EcampCore\Repository\PluginInstanceRepository
     */
    private $pluginInstanceRepository;

    /**
     * @return EcampCore\Repository\PluginInstanceRepository
     */
    public function getPluginInstanceRepository()
    {
        return $this->pluginInstanceRepository;
    }

    public function setPluginInstanceRepository($pluginInstanceRepository)
    {
        $this->pluginInstanceRepository = $pluginInstanceRepository;

        return $this;
    }
}
