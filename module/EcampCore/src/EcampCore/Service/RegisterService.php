<?php

namespace EcampCore\Service;

use EcampCore\Entity\User;
use EcampCore\Job\Mail\SendEmailVerificationEmailJobFactory;
use EcampCore\Job\Mail\SendPwResetMailJobFactory;
use EcampCore\Repository\UserRepository;
use EcampLib\Service\ExecutionException;
use EcampLib\Validation\ValidationException;
use Zend\Validator\EmailAddress;

class RegisterService extends Base\ServiceBase
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var UserService
     */
    private $userService;

    /**
     * @var LoginService
     */
    private $loginService;

    /**
     * @var ResqueJobService
     */
    private $resqueJobService;

    public function __construct(
        $userRepository, $userService, $loginService, $resqueJobService
    ){
        $this->userRepository = $userRepository;
        $this->userService = $userService;
        $this->loginService = $loginService;
        $this->resqueJobService = $resqueJobService;
    }

    /**
     * @param $data
     * @throws ValidationException
     * @return User
     */
    public function Register($data)
    {
        try {
            $user = $this->userService->Create($data['user-create']);
        } catch (ValidationException $ex) {
            throw ValidationException::FromInnerException('user-create', $ex);
        }

        if ($user->getState() != User::STATE_NONREGISTERED) {
            throw new ValidationException(array('mail' => "This eMail-Address is already registered!"));
        }

        $user->setState(User::STATE_REGISTERED);
        try {
            $this->loginService->Create($user, $data['login-create']);
        } catch (ValidationException $ex) {
            throw ValidationException::FromInnerException('login-create', $ex);
        }

        $this->resqueJobService->Create(
            SendEmailVerificationEmailJobFactory::class,
            array('userId' => $user->getId())
        );

        return $user;
    }

    /**
     * @param $userId
     * @param $key
     * @return bool
     * @throws \EcampLib\Service\ExecutionException
     */
    public function Activate($userId, $key)
    {
        /** @var $user \EcampCore\Entity\User */
        $user = $this->userRepository->findByIdentifier($userId);

        if (is_null($user)) {
            throw new ExecutionException("User not found!");
        } elseif ($user->getState() != User::STATE_REGISTERED) {
            throw new ExecutionException("User already activated!");
        } else {
            $success = $user->activateUser($key);
        }

        if ($success == false) {
            throw new ExecutionException("Wrong activation key!");
        }

        return $success;
    }



    public function ForgotPassword($data)
    {
        $email = $data['email'];

        $mailValidator = new EmailAddress();
        if (! $mailValidator->isValid($email)) {
            throw new ValidationException(array('email' => $mailValidator->getMessages()));
        }

        /** @var $user User */
        $user = $this->userRepository->findOneBy(array('email' => $email));

        if($user == null){
            throw new ValidationException(array('email' => 'Unknown email-address'));
        }

        $login = $user->getLogin();

        if($login == null){
            throw new ValidationException(array('email' => 'User has no login'));
        }

        $this->resqueJobService->Create(
            SendPwResetMailJobFactory::class,
            array('loginId' => $login->getId())
        );
    }


    public function ResetPassword($login, $resetKey, $password)
    {
        $login = $this->loginService->Get($login);

        $login->resetPassword($resetKey, $password);
    }


}
