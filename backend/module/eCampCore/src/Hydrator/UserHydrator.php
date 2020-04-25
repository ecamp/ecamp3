<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\User;
use Zend\Hydrator\HydratorInterface;

class UserHydrator implements HydratorInterface {
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
        /** @var User $user */
        $user = $object;

        return [
            'id' => $user->getId(),
            'username' => $user->getUsername(),
            'mail' => $user->getTrustedMailAddress(),
        ];
    }

    /**
     * @param object $object
     *
     * @return object
     */
    public function hydrate(array $data, $object) {
        /** @var User $user */
        $user = $object;

        $user->setUsername($data['username']);

        return $user;
    }
}
