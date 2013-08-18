<?php

namespace EcampApi\Auth;

use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Result;
use EcampCore\Entity\User;
use EcampApi\Entity\ApiKey;

class Adapter implements AdapterInterface
{

    const NOT_FOUND_MESSAGE 	= 'Unknown login!';
    const CREDINTIALS_MESSAGE 	= 'Wrong Key!';
    const NOT_ACTIVATED_MESSAGE = 'Account is not yet activated!';
    const UNKNOWN_FAILURE 		= 'Unknown error!';

    /**
     * @var ApiKey
     */
    private $apiKey;

    /**
     * @var string
     */
    private $key;

    /**
     * @param ApiKey $apiKey
     * @param string $key
     */
    public function __construct(ApiKey $apiKey = null, $key)
    {
        $this->apiKey = $apiKey;
        $this->key = $key;
    }

    public function authenticate()
    {
        // User not Found:
        if (is_null($this->apiKey)) {
            return $this->authResult(
                Result::FAILURE_IDENTITY_NOT_FOUND,
                self::NOT_FOUND_MESSAGE
            );
        }

        $user = $this->apiKey->getUser();

        // User not activated:
        if ($user->getState() != User::STATE_ACTIVATED) {
            return $this->authResult(
                Result::FAILURE_IDENTITY_AMBIGUOUS,
                self::NOT_ACTIVATED_MESSAGE
            );
        }

        // Wrong ApiKey...
        if (!$this->apiKey->checkApiKey($this->key)) {
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
     * @return Zend\Authentication\Result
     */
    private function authResult($code, $messages = array())
    {
        if (!is_array($messages)) {
            $messages = array($messages);
        }

        $userId = ($this->apiKey != null) ? $this->apiKey->getUser()->getId() : null;

        return new Result($code, $userId, $messages);
    }

}
