<?php

namespace eCamp\Core\Auth\Adapter;

use eCamp\Core\Entity\Login;
use eCamp\Core\Entity\User;
use eCamp\Core\EntityService\UserService;
use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Result;

class LoginPassword implements AdapterInterface {
    const NOT_FOUND_MESSAGE = 'Unknown login!';
    const CREDINTIALS_MESSAGE = 'Wrong Password!';
    const NOT_ACTIVATED_MESSAGE = 'Account is not yet activated!';
    const UNKNOWN_FAILURE = 'Unknown error!';

    /**
     * @var User $user
     */
    private $user;

    /**
     * @var Login $login
     */
    private $login;

    /**
     * @var string $password
     */
    private $password;


    /**
     * LoginPassword constructor.
     * @param $username
     * @param $password
     * @param UserService $userService
     */
    public function __construct($username, $password, UserService $userService) {
        /** @var User $user */
        $user = $userService->findByUsername($username);
        $this->login = ($user !== null) ? $user->getLogin() : null;
        $this->password = $password;
    }

    /**
     * Performs an authentication attempt
     *
     */
    public function authenticate() {
        // User Not Found:
        if (is_null($this->login)) {
            return $this->authResult(
                Result::FAILURE_IDENTITY_NOT_FOUND,
                self::NOT_FOUND_MESSAGE
            );
        }
        /** @var $user User */
        $this->user = $this->login->getUser();
        // User Not Activated:
        if ($this->user->getState() != User::STATE_ACTIVATED) {
            return $this->authResult(
                Result::FAILURE_IDENTITY_AMBIGUOUS,
                self::NOT_ACTIVATED_MESSAGE
            );
        }
        // User with wrong Password:
        if (!$this->login->checkPassword($this->password)) {
            return $this->authResult(
                Result::FAILURE_CREDENTIAL_INVALID,
                self::CREDINTIALS_MESSAGE
            );
        }
        // Successful logged in:
        return $this->authResult(Result::SUCCESS);
    }

    /**
     * Factory for Result
     *
     * @param integer    The Result code, see Zend_Auth_Result
     * @param mixed      The Message, can be a string or array
     * @return \Zend\Authentication\Result
     */
    private function authResult($code, $messages = array()) {
        if (!is_array($messages)) {
            $messages = array($messages);
        }
        $userId = ($this->user != null) ? $this->user->getId() : null;
        return new Result($code, $userId, $messages);
    }
}
