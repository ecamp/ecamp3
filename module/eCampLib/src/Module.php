<?php

namespace eCamp\Lib;

use eCamp\Lib\ServiceManager\EntityFilterManagerFactory;
use Zend\ModuleManager\Feature\InitProviderInterface;
use Zend\ModuleManager\ModuleManagerInterface;

class Module implements InitProviderInterface
{
    public function getConfig() {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function init(ModuleManagerInterface $manager) {
        EntityFilterManagerFactory::initModule($manager);
    }

}
