<?php

namespace eCamp\Lib\ServiceManager;

use Zend\Hydrator\HydratorPluginManager;

interface HydratorPluginManagerAware
{
    public function setHydratorPluginManager(HydratorPluginManager $hydratorPluginManager);
}
