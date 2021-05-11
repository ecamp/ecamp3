<?php

namespace eCamp\Core\Auth\Adapter;

use eCamp\Core\Entity\Login;
use eCamp\Core\Entity\User;
use Laminas\Authentication\Adapter\AdapterInterface;
use Laminas\Authentication\Result;

class LoginPassword implements AdapterInterface {
    public const NOT_FOUND_MESSAGE = 'Unknown login!';
    public const CREDINTIALS_MESSAGE = 'Wrong Password!';
    public const NOT_ACTIVATED_MESSAGE = 'Account is not yet activated!';
    public const UNKNOWN_FAILURE = 'Unknown error!';

    /**
     * @var User
     */
    private $user;

    /**
     * @var Login
     */
    private $login;

    /**
     * @var string
     */
    private $password;

    /**
     * LoginPassword constructor.
     *
     * @param User $user
     * @param $password
     */
    public function __construct(User $user = null, $password) {
        $this->login = (null !== $user) ? $user->getLogin() : null;
        $this->password = $password;
    }

    /**
     * Performs an authentication attempt.
     */
    public function authenticate(): Result {
        // User Not Found:
        if (is_null($this->login)) {
            return $this->authResult(
                Result::FAILURE_IDENTITY_NOT_FOUND,
                self::NOT_FOUND_MESSAGE
            );
        }
        // @var $user User
        $this->user = $this->login->getUser();
        // User Not Activated:
        if (User::STATE_ACTIVATED != $this->user->getState()) {
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
     * Factory for Result.
     *
     * @param int    The Result code, see Zend_Auth_Result
     * @param mixed      The Message, can be a string or array
     */
    private function authResult($code, $messages = []): Result {
        if (!is_array($messages)) {
            $messages = [$messages];
        }
        $userId = (null != $this->user) ? $this->user->getId() : null;

        return new Result($code, $userId, $messages);
    }
}
