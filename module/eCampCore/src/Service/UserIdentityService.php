<?php

namespace eCamp\Core\Service;

use eCamp\Core\Entity\UserIdentity;
use eCamp\Core\Hydrator\UserIdentityHydrator;
use eCamp\Lib\Service\BaseService;

class UserIdentityService extends BaseService
{
    public function __construct(UserIdentityHydrator $userIdentityHydrator) {
        parent::__construct($userIdentityHydrator, UserIdentity::class);
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
