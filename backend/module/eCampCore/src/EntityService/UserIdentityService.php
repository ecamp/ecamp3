<?php

namespace eCamp\Core\EntityService;

use eCamp\Core\Entity\User;
use eCamp\Core\Entity\UserIdentity;
use eCamp\Core\Hydrator\UserIdentityHydrator;
use eCamp\Lib\Service\ServiceUtils;
use Laminas\Authentication\AuthenticationService;

class UserIdentityService extends AbstractEntityService {
    /** @var UserService */
    protected $userService;

    public function __construct(
        ServiceUtils $serviceUtils,
        AuthenticationService $authenticationService,
        UserService $userService
    ) {
        parent::__construct(
            $serviceUtils,
            UserIdentity::class,
            UserIdentityHydrator::class,
            $authenticationService
        );

        $this->userService = $userService;
    }

    /**
     * @param $provider
     * @param $identifier
     *
     * @return null|object|UserIdentity
     */
    public function find($provider, $identifier) {
        return $this->getRepository()->findOneBy([
            'provider' => $provider,
            'providerId' => $identifier,
        ]);
    }

    /**
     * @param $provider
     * @param $profile
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \eCamp\Lib\Acl\NoAccessException
     *
     * @return User
     */
    public function findOrCreateUser($provider, $profile) {
        $identifier = $profile->identifier;
        // Look for existing identity in database
        $existingIdentity = $this->find($provider, $identifier);
        // Look for existing user in database
        /** @var User $user */
        $user = null;
        if ($existingIdentity) {
            $user = $existingIdentity->getUser();
        } else {
            $user = $this->userService->findByMail($profile->email);
        }
        // Create user if he doesn't exist yet
        if (null == $user) {
            $user = $this->userService->create($profile);
        } else {
            $userHydrator = $this->userService->getHydrator();
            $user = $userHydrator->hydrate(['username' => $profile->displayName], $user);
        }
        // Create identity if it doesn't exist yet
        if (null == $existingIdentity) {
            /** @var UserIdentity $existingIdentity */
            $existingIdentity = $this->create([
                'provider' => $provider,
                'providerId' => $profile->identifier,
            ]);
            $user->addUserIdentity($existingIdentity);
        }
        // Save to db and return results
        $this->getServiceUtils()->emFlush();

        return $user;
    }
}
