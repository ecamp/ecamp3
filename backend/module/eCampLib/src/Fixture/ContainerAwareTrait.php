<?php

namespace eCamp\Lib\Fixture;

use Interop\Container\ContainerInterface;

trait ContainerAwareTrait {
    private ContainerInterface $container;

    public function getContainer(): ContainerInterface {
        return $this->container;
    }

    public function setContainer(ContainerInterface $container): void {
        $this->container = $container;
    }
}
