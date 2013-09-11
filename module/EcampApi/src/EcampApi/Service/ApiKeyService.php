<?php

namespace EcampApi\Service;

use EcampLib\Service\ServiceBase;
use EcampCore\Acl\Privilege;
use EcampCore\Repository\UserRepository;
use EcampCore\Entity\User;
use EcampApi\Entity\ApiKey;
use Doctrine\ORM\EntityRepository;
use Zend\Authentication\AuthenticationService;

class ApiKeyService extends ServiceBase
{

    /**
     * @var \Doctrine\Orm\EntityRepository
     */
    private $apiKeyRepository;

    /**
     * @var \EcampCore\Repository\UserRepository
     */
    private $userRepo;

    public function __construct(
        EntityRepository $apiKeyRepository,
        UserRepository $userRepo
    ){
        $this->apiKeyRepository = $apiKeyRepository;
        $this->userRepo = $userRepo;
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
        $user = $this->userRepo->findByIdentifier($identifier);

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
