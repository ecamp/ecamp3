<?php

namespace EcampCore\Service;

use EcampCore\Entity\AutoLogin;
use EcampCore\Entity\User;
use EcampCore\Entity\Login;
use EcampCore\Repository\AutoLoginRepository;
use EcampCore\Repository\LoginRepository;
use EcampCore\Repository\UserRepository;
use EcampLib\Service\ServiceBase;
use EcampLib\Validation\ValidationException;
use Zend\Authentication\AuthenticationService;

class LoginService
    extends ServiceBase
{
    /**
     * @var \EcampCore\Repository\LoginRepository
     */
    private $loginRepository;

    /**
     * @var \EcampCore\Repository\UserRepository
     */
    private $userRepository;

    /**
     * @var \EcampCore\Repository\AutoLoginRepository
     */
    private $autoLoginRepository;

    public function __construct(
        LoginRepository $loginRepository,
        AutoLoginRepository $autoLoginRepository,
        UserRepository $userRepository
    ){
        $this->loginRepository = $loginRepository;
        $this->autoLoginRepository = $autoLoginRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @param  null       $login
     * @return Login|NULL
     */
    public function Get($login = null)
    {
        if (isset($login)) {
            if ($login instanceof Login) {
                return $login;
            } else {
                return $this->loginRepository->find($login);
            }
        }

        $user = $this->getMe();
        if (!is_null($user)) {
            return $user->getLogin();
        }

        return null;
    }

    /**
     * @param User $user
     * @param $data
     * @return Login
     * @throws ValidationException
     */
    public function Create(User $user, $data)
    {
        if ($user->getLogin() != null) {
            // TODO: log!
            throw new ValidationException(array('user' => "User has already a Login"));
        }

        $login = new Login($user);
        $login->setNewPassword($data['password1']);
        $this->persist($login);

        return $login;
    }

    public function Delete()
    {
        $me = $this->getMe();
        $login = $me->getLogin();

        if (is_null($login)) {
            $this->addValidationMessage("There is no Login to be deleted!");
        } else {
            $this->remove($login);
        }
    }

    /**
     * @param $identifier
     * @param $password
     * @return \Zend\Authentication\Result
     */
    public function Login($identifier, $password)
    {
        /** @var \EcampCore\Entity\User  */
        $user = $this->userRepository->findByIdentifier($identifier);

        if (is_null($user)) {
            $login = null;
        } else {
            /** @var Login */
            $login = $user->getLogin();
        }

        $authAdapter = new \EcampCore\Auth\LoginPasswordAdapter($login, $password);
        $authService = new AuthenticationService();
        $result = $authService->authenticate($authAdapter);

        return $result;
    }

    public function Logout()
    {
        $authService = new AuthenticationService();
        $authService->clearIdentity();
    }

    public function ChangePassword($login, $oldPassword, $newPassword)
    {
        $login = $this->Get($login);

        if ($login->checkPassword($oldPassword)) {
            $login->setNewPassword($newPassword);
        } else {
            throw ValidationException::Create(
                array('password' => array('Password not correct'))
            );
        }
    }

    public function ResetPassword($pwResetKey, Params $params)
    {
        $login = $this->getLoginByResetKey($pwResetKey);
        $loginValidator = new \EcampCore\Validate\LoginValidator($login);

        if (is_null($login)) {
            $this->addValidationMessage("No Login found for given PasswordResetKey");
        }

        $this->validationFailed(
            ! $loginValidator->isValid($params));

        $login->setNewPassword($params->getValue('password'));
        $login->clearPwResetKey();
    }

    public function ForgotPassword($identifier)
    {
        $user = $this->userRepository->findByIdentifier($identifier);

        if (is_null($user)) {
            return false;
        }

        $login = $user->getLogin();

        if (is_null($login)) {
            return false;
        }

        $login->createPwResetKey();
        $resetKey = $login->getPwResetKey();

        //TODO: Send Mail with Link to Reset Password.
        return $resetKey;
    }

    /**
     * @return \Zend\Authentication\Result
     */
    public function AutoLogin($token)
    {
        $autoLogin = $this->autoLoginRepository->findByToken($token);

        $authAdapter = new \EcampCore\Auth\AutologinAdapter($autoLogin);
        $authService = new AuthenticationService();
        $result = $authService->authenticate($authAdapter);

        return $result;
    }

    /**
     * @param  User   $user
     * @return string
     */
    public function CreateAutoLoginToken(User $user)
    {
        $autoLogin = new AutoLogin($user);
        $this->persist($autoLogin);

        return $autoLogin->createToken();
    }

    /**
     * Returns the LoginEntity with the given pwResetKey
     *
     * @param  string $pwResetKey
     * @return Login
     */
    private function getLoginByResetKey($pwResetKey)
    {
        /** @var Login $login */
        $login = $this->loginRepository->findOneBy(array('pwResetKey' => $pwResetKey));

        return $login;
    }
}
