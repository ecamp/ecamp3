<?php

namespace eCamp\Lib\Mail;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class ElasticMailFactory implements FactoryInterface {
    /**
     * @param string $requestedName
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ProviderInterface {
        $transport = $container->get('SlmMail\Mail\Transport\ElasticEmailTransport');

        return new ElasticMail($transport);
    }
}
