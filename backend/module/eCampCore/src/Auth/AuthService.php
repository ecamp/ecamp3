<?php

namespace eCamp\Core\Auth;

use eCamp\Core\Auth\Adapter\LoginPassword;
use eCamp\Core\Entity\User;
use eCamp\Core\Repository\UserRepository;
use Hybridauth\Adapter\AdapterInterface;
use Hybridauth\Exception\InvalidArgumentException;
use Hybridauth\Exception\UnexpectedValueException;
use Hybridauth\Hybridauth;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Result;

class AuthService extends AuthenticationService {
    /** @var UserRepository */
    private $userRepository;

    /** @var array */
    private $hybridAuthConfig;


    public function __construct(
        UserRepository $userRepository,
        array $hybridAuthConfig
    ) {
        parent::__construct();

        $this->userRepository = $userRepository;
        $this->hybridAuthConfig = $hybridAuthConfig;
    }

    /** @return string */
    public function getAuthUserId() {
        return $this->getIdentity();
    }

    /** @return User */
    public function getAuthUser() {
        $id = $this->getAuthUserId();
        $user = null;

        if ($id != null) {
            /** @var User $user */
            $user = $this->userRepository->find($id);
        }

        return $user;
    }


    /**
     * @param $username
     * @param $password
     * @return Result
     */
    public function login($username, $password) {
        /** @var User $user */
        $user = $this->userRepository->findByUsername($username);
        $login = ($user !== null) ? $user->getLogin() : null;

        $adapter = new LoginPassword($login, $password);

        return $this->authenticate($adapter);
    }


    /**
     * @param $providerName
     * @param array $config
     * @return AdapterInterface
     * @throws InvalidArgumentException
     * @throws UnexpectedValueException
     */
    public function createOAuthAdapter($providerName, array $config) {
        $hybridAuthConfig = $this->hybridAuthConfig + $config;
        $hybridauth = new Hybridauth($hybridAuthConfig);

        return $hybridauth->getAdapter($providerName);
    }
}
