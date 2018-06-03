<?php

namespace eCamp\Core\EntityService;

use eCamp\Core\Entity\UserIdentity;
use eCamp\Core\Hydrator\UserIdentityHydrator;

class UserIdentityService extends AbstractEntityService
{
    public function __construct(UserIdentityHydrator $userIdentityHydrator)
    {
        parent::__construct(
            UserIdentity::class,
            UserIdentityHydrator::class
        );
    }

    /**
     * @param $provider
     * @param $identifier
     * @return UserIdentity|null|object
     */
    public function find($provider, $identifier)
    {
        return $this->getRepository()->findOneBy([
            'provider' => $provider,
            'providerId' => $identifier
        ]);
    }
}
