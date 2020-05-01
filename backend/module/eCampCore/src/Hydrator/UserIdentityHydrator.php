<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\UserIdentity;
use Zend\Hydrator\HydratorInterface;

class UserIdentityHydrator implements HydratorInterface {
    public static function HydrateInfo() {
        return [
        ];
    }

    /**
     * @param object $object
     *
     * @return array
     */
    public function extract($object) {
        /** @var UserIdentity $userIdentity */
        $userIdentity = $object;

        return [
            'id' => $userIdentity->getId(),
        ];
    }

    /**
     * @param object $object
     *
     * @return object
     */
    public function hydrate(array $data, $object) {
        /** @var UserIdentity $userIdentity */
        $userIdentity = $object;

        if (isset($data['provider'])) $userIdentity->setProvider($data['provider']);
        if (isset($data['providerId'])) $userIdentity->setProviderId($data['providerId']);

        return $userIdentity;
    }
}
