<?php
namespace EcampApi\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ApiKeyServiceFactory
    implements FactoryInterface
{
    /**
     * @param  ServiceLocatorInterface              $services
     * @return ApiKeyService
     * @throws Exception\ServiceNotCreatedException
     */
    public function createService(ServiceLocatorInterface $services)
    {
        $apiKeyRepository = $services->get('EcampApi\Repository\ApiKey');
        $userRepo = $services->get('EcampCore\Repository\User');

        return new ApiKeyService($apiKeyRepository, $userRepo);
    }
}
