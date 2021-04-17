<?php

namespace eCamp\Lib\Fixture;

use Interop\Container\ContainerInterface;

interface ContainerAwareInterface {
    public function getContainer(): ContainerInterface;

    public function setContainer(ContainerInterface $container);
}
