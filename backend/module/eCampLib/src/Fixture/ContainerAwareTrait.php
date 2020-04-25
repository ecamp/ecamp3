<?php

namespace eCamp\Lib\Fixture;

use Interop\Container\ContainerInterface;

trait ContainerAwareTrait {
    /** @var ContainerInterface */
    private $container;

    public function getContainer() {
        return $this->container;
    }

    public function setContainer(ContainerInterface $container) {
        $this->container = $container;
    }
}
