<?php

namespace EcampCore\Auth;

use EcampCore\Entity\AutoLogin;
use EcampCore\Entity\User;
use Zend\Authentication\Result;
use Zend\Authentication\Adapter\AdapterInterface;

class AutologinAdapter
    implements AdapterInterface
{

    const NOT_FOUND_MESSAGE 	= 'No Autologin given!';
    const NOT_ACTIVATED_MESSAGE = 'Account is not yet activated!';
    const UNKNOWN_FAILURE 		= 'Unknown error!';

    /**
     * @var \EcampCore\Entity\User $user
     */
    private $user;

    /**
     * @var AutoLogin
     */
    private $autologin;

    public function __construct(AutoLogin $autologin)
    {
        $this->autologin = $autologin;
    }

    /**
     * Performs an authentication attempt
     */
    public function authenticate()
    {
        // No AutologinToken given:
        if (is_null($this->autologin)) {
            return $this->authResult(
                Result::FAILURE_UNCATEGORIZED,
                self::NOT_FOUND_MESSAGE
            );
        }

        $this->user = $this->autologin->getUser();

        // User Not Activated:
        if ($this->user->getState() != User::STATE_ACTIVATED) {
            return $this->authResult(
                Result::FAILURE_UNCATEGORIZED,
                self::NOT_ACTIVATED_MESSAGE
            );
        }

        // Successful logged in:
        return $this->authResult(Result::SUCCESS);
    }

    /**
     * @param $code
     * @param  array  $messages
     * @return Result
     */
    private function authResult($code, $messages = array())
    {
        if (!is_array($messages)) {
            $messages = array($messages);
        }

        $userId = ($this->user != null) ? $this->user->getId() : null;

        return new Result($code, $userId, $messages);
    }
}
