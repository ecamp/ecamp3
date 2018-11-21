<?php

namespace eCamp\Core\EntityService;

use eCamp\Core\Entity\UserIdentity;
use eCamp\Core\Hydrator\UserIdentityHydrator;
use eCamp\Lib\Service\ServiceUtils;
use Zend\Authentication\AuthenticationService;

class UserIdentityService extends AbstractEntityService {
    public function __construct(ServiceUtils $serviceUtils, AuthenticationService $authenticationService) {
        parent::__construct(
            $serviceUtils,
            UserIdentity::class,
            UserIdentityHydrator::class,
            $authenticationService
        );
    }

    /**
     * @param $provider
     * @param $identifier
     * @return UserIdentity|null|object
     */
    public function find($provider, $identifier) {
        return $this->getRepository()->findOneBy([
            'provider' => $provider,
            'providerId' => $identifier
        ]);
    }
}
