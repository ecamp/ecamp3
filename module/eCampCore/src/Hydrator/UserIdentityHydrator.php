<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\User;
use eCamp\Core\Entity\UserIdentity;
use Zend\Hydrator\HydratorInterface;

class UserIdentityHydrator implements HydratorInterface
{
    /**
     * @param object $object
     * @return array
     */
    public function extract($object)
    {
        /** @var UserIdentity $userIdentity */
        $userIdentity = $object;
        return [
            'id' => $userIdentity->getId(),
        ];
    }

    /**
     * @param array $data
     * @param object $object
     * @return object
     */
    public function hydrate(array $data, $object)
    {
        /** @var UserIdentity $userIdentity */
        $userIdentity = $object;

        $userIdentity->setProvider($data['provider']);
        $userIdentity->setProviderId($data['providerId']);

        return $userIdentity;
    }
}
