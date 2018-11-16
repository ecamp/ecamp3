<?php

namespace eCamp\Api;

use Zend\Config\Factory;

class Module {
    public function getConfig() {
        return Factory::fromFiles(array_merge(
            [__DIR__ . '/../config/module.config.php'],
            glob(__DIR__ . '/../config/autoload/*.*'),
            glob(__DIR__ . '/../config/autoload/V1/*.*')
        ));
    }
}
