<?php

namespace eCamp\Lib\Mail;

use Interop\Container\ContainerInterface;
use Laminas\Mail\Transport\Factory;
use Laminas\ServiceManager\Factory\FactoryInterface;

class TransportFactory implements FactoryInterface {
    /**
     * @param string $requestedName
     *
     * @return \Laminas\Mail\Transport\TransportInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $config = $container->get('Config');
        $transportConfig = $config['zend_mail']['transport'] ?: [];

        return Factory::create($transportConfig);
    }
}
