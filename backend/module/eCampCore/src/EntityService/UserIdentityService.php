<?php

namespace eCamp\Core\EntityService;

use Doctrine\ORM\ORMException;
use eCamp\Core\Entity\User;
use eCamp\Core\Entity\UserIdentity;
use eCamp\Core\Hydrator\UserIdentityHydrator;
use eCamp\Lib\Acl\NoAccessException;
use eCamp\Lib\Service\ServiceUtils;
use Hybridauth\User\Profile;
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
     * @param string $provider
     *
     * @throws NoAccessException
     * @throws ORMException
     *
     * @return User
     */
    public function findOrCreateUser($provider, Profile $profile) {
        $identifier = $profile->identifier;
        // Look for existing identity in database
        $existingIdentity = $this->find($provider, $identifier);

        // Look for existing user in database
        /** @var User $user */
        $user = null;
        if ($existingIdentity) {
            $user = $existingIdentity->getUser();
        } else {
            $user = $this->userService->findByMail($profile->emailVerified);
        }

        if (null === $user) {
            // Create user if he doesn't exist yet
            $user = $this->userService->create($profile);
        } else {
            // Update user
            // Is this necessary?
            $userHydrator = $this->userService->getHydrator();
            $user = $userHydrator->hydrate([
                'firstname' => $profile->firstName,
                'surname' => $profile->lastName,
                'username' => $profile->displayName,
            ], $user);
        }

        // Create identity if it doesn't exist yet
        if (null === $existingIdentity) {
            $this->create([
                'provider' => $provider,
                'providerId' => $profile->identifier,
                'user' => $user,
            ]);
        }

        return $user;
    }

    /**
     * @param $provider
     * @param $identifier
     *
     * @return null|object|UserIdentity
     */
    protected function find($provider, $identifier) {
        return $this->getRepository()->findOneBy([
            'provider' => $provider,
            'providerId' => $identifier,
        ]);
    }

    /**
     * @param array $data
     * @param User  $user
     *
     * @return UserIdentity
     */
    protected function createEntity($data) {
        /** @var UserIdentity $identity */
        $identity = parent::createEntity($data);

        /** @var User $user */
        $user = $data['user'];
        $user->addUserIdentity($identity);

        return $identity;
    }
}
