<?php

namespace eCamp\Core\EntityService;

use Doctrine\ORM\ORMException;
use eCamp\Core\Entity\User;
use eCamp\Core\Entity\UserIdentity;
use eCamp\Core\Hydrator\UserHydrator;
use eCamp\Core\Hydrator\UserIdentityHydrator;
use eCamp\Lib\Acl\NoAccessException;
use eCamp\Lib\Service\ServiceUtils;
use Hybridauth\User\Profile;
use Laminas\Authentication\AuthenticationService;

class UserIdentityService extends AbstractEntityService {
    protected UserService $userService;

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
     */
    public function findOrCreateUser($provider, Profile $profile): User {
        $identifier = $profile->identifier;
        // Look for existing identity in database
        $existingIdentity = $this->find($provider, $identifier);

        // Look for existing user in database
        /** @var User $user */
        $user = null;
        if ($existingIdentity) {
            $user = $existingIdentity->getUser();
        } else {
            $user = $this->userService->findByTrustedMail($profile->emailVerified);
        }

        if (null === $user) {
            // Create user if he doesn't exist yet
            $user = $this->userService->create($profile);
        } else {
            // Update user
            // Is this necessary?
            $userHydrator = new UserHydrator();
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
     */
    protected function find($provider, $identifier): ?UserIdentity {
        return $this->getRepository()->findOneBy([
            'provider' => $provider,
            'providerId' => $identifier,
        ]);
    }

    /**
     * @param array $data
     * @param User  $user
     */
    protected function createEntity($data): UserIdentity {
        /** @var UserIdentity $identity */
        $identity = parent::createEntity($data);

        /** @var User $user */
        $user = $data['user'];
        $user->addUserIdentity($identity);

        return $identity;
    }
}
