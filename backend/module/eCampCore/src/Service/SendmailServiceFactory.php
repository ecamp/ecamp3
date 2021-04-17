<?php

namespace eCamp\Core\Service;

use eCamp\Lib\Mail\ProviderInterface;
use eCamp\Lib\Service\ServiceUtils;
use Interop\Container\ContainerInterface;
use Laminas\Authentication\AuthenticationService;
use Laminas\ServiceManager\Factory\FactoryInterface;

class SendmailServiceFactory implements FactoryInterface {
    /**
     * @param string $requestedName
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): SendmailService {
        $serviceUtil = $container->get(ServiceUtils::class);
        $authService = $container->get(AuthenticationService::class);
        $mailProvider = $container->get(ProviderInterface::class);
        $from = 'sender';

        $config = $container->get('config');
        $from = $config['ecamp']['mail']['from'];
        $frontendUrl = $config['ecamp']['frontend']['url'];

        return new SendmailService($serviceUtil, $authService, $mailProvider, $from, $frontendUrl);
    }
}
