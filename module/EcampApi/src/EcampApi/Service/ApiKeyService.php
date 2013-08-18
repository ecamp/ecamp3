<?php

namespace EcampApi\Service;

use EcampLib\Service\ServiceBase;
use EcampCore\Service\UserService;
use EcampCore\Entity\User;
use EcampApi\Entity\ApiKey;
use EcampCore\Acl\Privilege;
use Doctrine\ORM\EntityRepository;
use Zend\Authentication\AuthenticationService;

class ApiKeyService extends ServiceBase
{

    /**
     * @var \Doctrine\Orm\EntityRepository
     */
    private $apiKeyRepository;

    /**
     * @var \EcampCore\Service\UserService
     */
    private $userService;

    public function __construct(
        EntityRepository $apiKeyRepository,
        UserService $userService
    ){
        $this->apiKeyRepository = $apiKeyRepository;
        $this->userService = $userService;
    }

    /**
     * @param  string $appName
     * @param  string $deviceName
     * @return string
     */
    public function CreateApiKey($appName, $deviceName = null)
    {
        $this->aclRequire($this->getMe(), Privilege::USER_ADMINISTRATE);

        // If ApiKey exists allready, return the existing Key.
        $criteria = array(
                'user' => $this->getMe(),
                'appName' => $appName,
                'deviceName' => $deviceName
        );
        $apiKey = $this->apiKeyRepository->findOneBy($criteria);

        if ($apiKey == null) {
            $apiKey = new ApiKey($this->getMe(), $appName, $deviceName);
            $this->persist($apiKey);
        }

        return $apiKey->getApiKey();
    }

    public function Login($identifier, $key, $appName, $deviceName = null)
    {
        $user = $this->userService->Get($identifier);

        $criteria = array(
            'user' => $user,
            'appName' => $appName,
            'deviceName' => $deviceName
        );

        $apiKey = $this->apiKeyRepository->findOneBy($criteria);

        $authAdapter = new \EcampApi\Auth\Adapter($apiKey, $key);
        $authService = new AuthenticationService();
        $result = $authService->authenticate($authAdapter);

        return $result;
    }

    public function Logout()
    {
        $authService = new AuthenticationService();
        $authService->clearIdentity();
    }

}
