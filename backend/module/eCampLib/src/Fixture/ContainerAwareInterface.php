<?php

namespace eCamp\Lib\Fixture;

use Interop\Container\ContainerInterface;

interface ContainerAwareInterface {
    public function getContainer();

    public function setContainer(ContainerInterface $container);
}
