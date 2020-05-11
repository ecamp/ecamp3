<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\User;
use Laminas\Authentication\AuthenticationService;
use Laminas\Hydrator\HydratorInterface;

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
        $auth = new AuthenticationService();
        /** @var User $user */
        $user = $object;

        $relation = $user->getRelation($auth->getIdentity());
        $showDetails = (User::RELATION_UNRELATED != $relation);

        return [
            'id' => $user->getId(),
            'username' => $user->getUsername(),
            'mail' => $showDetails ? $user->getTrustedMailAddress() : '***',
            'relation' => $relation,
            'role' => $user->getRole(),
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

        if (isset($data['username'])) {
            $user->setUsername($data['username']);
        }

        return $user;
    }
}
