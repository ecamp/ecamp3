<?php

namespace eCamp\Core\EntityService;

use eCamp\Core\Entity\User;
use eCamp\Core\Entity\UserIdentity;
use eCamp\Core\Hydrator\UserIdentityHydrator;
use eCamp\Lib\Service\ServiceUtils;
use Zend\Authentication\AuthenticationService;

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
     * @return UserIdentity|null|object
     */
    public function find($provider, $identifier) {
        return $this->getRepository()->findOneBy([
            'provider' => $provider,
            'providerId' => $identifier
        ]);
    }

    /**
     * @param $provider
     * @param $profile
     * @throws \Doctrine\ORM\ORMException
     * @throws \eCamp\Lib\Acl\NoAccessException
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
        if ($user == null) {
            $user = $this->userService->create($profile);
        } else {
            $user = $this->userService->update($user, $profile);
        }
        // Create identity if it doesn't exist yet
        if ($existingIdentity == null) {
            /** @var UserIdentity $existingIdentity */
            $existingIdentity = $this->create([
                'provider' => $provider,
                'providerId' => $profile->identifier
            ]);
            $existingIdentity->setUser($user);
        }
        // Save to db and return results
        $this->getServiceUtils()->emFlush();
        return $user;
    }
}
