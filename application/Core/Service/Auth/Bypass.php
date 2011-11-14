<?php
/**
 * Created by JetBrains PhpStorm.
 * User: pirminmattmann
 * Date: 05.04.11
 * Time: 23:18
 * To change this template use File | Settings | File Templates.
 */

namespace Core\Service\Auth;

class Bypass
    implements \Zend_Auth_Adapter_Interface
{

    const NOT_FOUND_MESSAGE 	= 'Unknown login!';
    const CREDINTIALS_MESSAGE 	= 'Wrong Password!';
    const NOT_ACTIVATED_MESSAGE = 'Account is not yet activated!';
    const UNKNOWN_FAILURE 	= 'Unknown error!';

    private $em;

    private $user;


    public function __construct($user)
    {
        $this->em = \Zend_Registry::get('doctrine')->getEntityManager();

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
        if(is_null($user))
        {
            return $this->authResult(
                \Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND,
                self::NOT_FOUND_MESSAGE
            );
        }


        // User Not Activated:
        if($user->getState() != \Core\Entity\User::STATE_ACTIVATED || is_null($user->getLogin()))
        {
            return $this->authResult(
                \Zend_Auth_Result::FAILURE_IDENTITY_AMBIGUOUS,
                self::NOT_ACTIVATED_MESSAGE
            );
        }

        // Successful logged in:
        $this->login = $user->getLogin()->getId();

        return $this->authResult(\Zend_Auth_Result::SUCCESS);
    }


     /**
     * Factory for Zend_Auth_Result
     *
     *@param integer    The Result code, see Zend_Auth_Result
     *@param mixed      The Message, can be a string or array
     *@return Zend_Auth_Result
     */
    private function authResult($code, $messages = array())
	{
        if( !is_array( $messages ) )
        {	$messages = array($messages);	}

		return new \Zend_Auth_Result($code, $this->login, $messages);
    }
}
