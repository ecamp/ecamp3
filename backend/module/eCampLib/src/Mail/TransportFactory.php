<?php

namespace eCamp\Lib\Mail;

use Interop\Container\ContainerInterface;
use Zend\Mail\Transport\Factory;
use Zend\ServiceManager\Factory\FactoryInterface;

class TransportFactory implements FactoryInterface {
    /**
     * @param string $requestedName
     *
     * @return \Zend\Mail\Transport\TransportInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $config = $container->get('Config');
        $transportConfig = $config['zend_mail']['transport'] ?: [];

        return Factory::create($transportConfig);
    }
}
