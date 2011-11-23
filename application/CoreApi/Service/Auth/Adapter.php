<?php
/**
 * Created by JetBrains PhpStorm.
 * User: pirminmattmann
 * Date: 05.04.11
 * Time: 23:18
 * To change this template use File | Settings | File Templates.
 */

namespace CoreApi\Service\Auth;

class Adapter
    implements \Zend_Auth_Adapter_Interface
{

    const NOT_FOUND_MESSAGE 	= 'Unknown login!';
    const CREDINTIALS_MESSAGE 	= 'Wrong Password!';
    const NOT_ACTIVATED_MESSAGE = 'Account is not yet activated!';
    const UNKNOWN_FAILURE 		= 'Unknown error!';


    /** @var \Core\Entity\User $user */
    private $user;
    
    /** @var \Core\Entity\Login $login */
    private $login;
    
    /** @var string $password */
    private $password;


    public function __construct(\Core\Entity\Login $login, $password)
    {
        $this->login = $login;
        $this->password = $password;
    }

    /**
     * Performs an authentication attempt
     *
     * @throws Zend_Auth_Adapter_Exception If authentication cannot be performed
     * @return Zend_Auth_Result
     */
    public function authenticate()
    {
        // User Not Found:
        if(is_null($this->login))
        {
            return $this->authResult(
                \Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND,
                self::NOT_FOUND_MESSAGE
            );
        }
        
        
        /** @var $user \Entity\User */
        $this->user = $this->login->getUser();


        // User Not Activated:
        if($this->user->getState() != \Core\Entity\User::STATE_ACTIVATED)
        {
            return $this->authResult(
                \Zend_Auth_Result::FAILURE_IDENTITY_AMBIGUOUS,
                self::NOT_ACTIVATED_MESSAGE
            );
        }


        // User with wrong Password:
        if(!$this->login->checkPassword($this->password))
        {
            return $this->authResult(
                \Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID,
                self::CREDINTIALS_MESSAGE
            );
        }

        // Successful logged in:
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

		return new \Zend_Auth_Result($code, $this->user->getId(), $messages);
    }
}
