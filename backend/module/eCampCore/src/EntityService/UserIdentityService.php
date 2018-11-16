<?php

namespace eCamp\Core\EntityService;

use eCamp\Core\Auth\AuthService;
use eCamp\Core\Entity\UserIdentity;
use eCamp\Core\Hydrator\UserIdentityHydrator;
use eCamp\Lib\Service\ServiceUtils;

class UserIdentityService extends AbstractEntityService {
    public function __construct(ServiceUtils $serviceUtils, AuthService $authService) {
        parent::__construct(
            $serviceUtils,
            $authService,
            UserIdentity::class,
            UserIdentityHydrator::class
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
