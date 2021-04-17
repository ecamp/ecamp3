<?php

namespace eCamp\Lib\Mail;

use Interop\Container\ContainerInterface;
use Laminas\Mail\Transport\Factory;
use Laminas\Mail\Transport\TransportInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class TransportFactory implements FactoryInterface {
    /**
     * @param string $requestedName
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): TransportInterface {
        $config = $container->get('Config');
        $transportConfig = $config['laminas_mail']['transport'] ?: [];

        return Factory::create($transportConfig);
    }
}
