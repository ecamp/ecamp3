<?php

namespace EcampCore\Service;

use EcampCore\Auth\LoginPasswordAdapter;
use EcampCore\Entity\Autologin;
use EcampCore\Entity\User;
use EcampCore\Entity\Login;
use EcampCore\Repository\AutologinRepository;
use EcampCore\Repository\LoginRepository;
use EcampCore\Repository\UserRepository;
use EcampLib\Validation\ValidationException;
use Zend\Authentication\AuthenticationService;

class LoginService extends Base\ServiceBase
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
     * @var \EcampCore\Repository\AutologinRepository
     */
    private $autologinRepository;

    public function __construct(
        LoginRepository $loginRepository,
        AutologinRepository $autologinRepository,
        UserRepository $userRepository
    ){
        $this->loginRepository = $loginRepository;
        $this->autologinRepository = $autologinRepository;
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
            throw new ValidationException(array('user' => "User has already a Login"));
        }

        $login = new Login($user, $data['password1']);
        $this->persist($login);

        return $login;
    }

    /**
     * @param User $user
     * @throws ValidationException
     */
    public function Delete(User $user)
    {
        $login = $user->getLogin();

        if ($login == null) {
            throw new ValidationException(array('login' => 'User has no Login'));
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

        $login = ($user != null) ? $user->getLogin() : null;

        $authAdapter = new LoginPasswordAdapter($login, $password);
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
        $login->changePassword($oldPassword, $newPassword);
    }


    /**
     * @return \Zend\Authentication\Result
     */
    public function Autologin($token)
    {
        $autoLogin = $this->autologinRepository->findByToken($token);

        $authAdapter = new \EcampCore\Auth\AutologinAdapter($autoLogin);
        $authService = new AuthenticationService();
        $result = $authService->authenticate($authAdapter);

        return $result;
    }

    /**
     * @param  User   $user
     * @return string
     */
    public function CreateAutologinToken(User $user)
    {
        $autoLogin = new Autologin($user);
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
