<?php
namespace EcampApi\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ApiKeyServiceFactory
    implements FactoryInterface
{
    /**
     *
     * @param  ServiceLocatorInterface              $services
     * @return AvatarService
     * @throws Exception\ServiceNotCreatedException
     */
    public function createService(ServiceLocatorInterface $services)
    {
        $apiKeyRepository = $services->get('EcampApi\Repository\ApiKey');
        $userService = $services->get('EcampCore\Service\User');

        return new ApiKeyService($apiKeyRepository, $userService);
    }
}
