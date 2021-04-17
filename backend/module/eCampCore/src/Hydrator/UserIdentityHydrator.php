<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\UserIdentity;
use Laminas\Hydrator\HydratorInterface;

class UserIdentityHydrator implements HydratorInterface {
    public static function HydrateInfo(): array {
        return [
        ];
    }

    /**
     * @param object $object
     */
    public function extract($object): array {
        /** @var UserIdentity $userIdentity */
        $userIdentity = $object;

        return [
            'id' => $userIdentity->getId(),
        ];
    }

    /**
     * @param object $object
     */
    public function hydrate(array $data, $object): UserIdentity {
        /** @var UserIdentity $userIdentity */
        $userIdentity = $object;

        if (isset($data['provider'])) {
            $userIdentity->setProvider($data['provider']);
        }
        if (isset($data['providerId'])) {
            $userIdentity->setProviderId($data['providerId']);
        }

        return $userIdentity;
    }
}
