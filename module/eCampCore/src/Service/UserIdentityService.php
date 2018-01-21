<?php

namespace eCamp\Core\Service;

use Doctrine\ORM\EntityManager;
use eCamp\Core\Entity\UserIdentity;
use eCamp\Core\Hydrator\UserIdentityHydrator;
use eCamp\Lib\Acl\Acl;
use eCamp\Lib\Service\BaseService;

class UserIdentityService extends BaseService
{
    public function __construct
    ( Acl $acl
    , EntityManager $entityManager
    , UserIdentityHydrator $userIdentityHydrator
    ) {
        parent::__construct
        ( $acl
        , $entityManager
        , $userIdentityHydrator
        , UserIdentity::class
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
