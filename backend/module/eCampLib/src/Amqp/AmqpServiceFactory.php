<?php

namespace eCamp\Lib\Amqp;

use Enqueue\AmqpBunny\AmqpConnectionFactory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class AmqpServiceFactory implements FactoryInterface {
    /**
     * @param string $requestedName
     *
     * @return AmqpService
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $config = $container->get('Config');
        $amqpConfig = $config['amqp']['connection'] ?: [];

        $factory = new AmqpConnectionFactory([
            'host' => $amqpConfig['host'] ?? 'rabbitmq',
            'port' => $amqpConfig['port'] ?? 5672,
            'vhost' => $amqpConfig['vhost'] ?? '/',
            'user' => $amqpConfig['user'] ?? 'guest',
            'pass' => $amqpConfig['pass'] ?? 'guest',
            'persisted' => $amqpConfig['persisted'] ?? false,
        ]);

        return new AmqpService( $factory->createContext() );
    }
}
