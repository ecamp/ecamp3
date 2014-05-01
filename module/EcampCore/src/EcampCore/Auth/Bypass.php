<?php
/**
 * Created by JetBrains PhpStorm.
 * User: pirminmattmann
 * Date: 05.04.11
 * Time: 23:18
 * To change this template use File | Settings | File Templates.
 */

namespace EcampCore\Auth;

use EcampCore\Entity\User;
use Zend\Authentication\Result;
use Zend\Authentication\Adapter\AdapterInterface;

class Bypass
    implements AdapterInterface
{

    const NOT_FOUND_MESSAGE 	= 'Unknown login!';
    const CREDINTIALS_MESSAGE 	= 'Wrong Password!';
    const NOT_ACTIVATED_MESSAGE = 'Account is not yet activated!';
    const UNKNOWN_FAILURE 		= 'Unknown error!';

    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Performs an authentication attempt
     *
     * @throws Zend_Auth_Adapter_Exception If authentication cannot be performed
     * @return Zend_Auth_Result
     */
    public function authenticate()
    {
        $user = $this->user;

        // User Not Found:
        if (is_null($user)) {
            return $this->authResult(
                Result::FAILURE_IDENTITY_NOT_FOUND,
                self::NOT_FOUND_MESSAGE
            );
        }

        // User Not Activated:
        if ($user->getState() != User::STATE_ACTIVATED || is_null($user->getLogin())) {
            return $this->authResult(
                Result::FAILURE_IDENTITY_AMBIGUOUS,
                self::NOT_ACTIVATED_MESSAGE
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
    private function authResult($code, $messages = array())
    {
        if ( !is_array( $messages ) ) {	$messages = array($messages);	}

        return new Result($code, $this->user->getId(), $messages);
    }
}
