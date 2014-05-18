<?php

namespace EcampCore\Service;

use EcampCore\Entity\User;
use EcampCore\Job\SendActivationMailJob;
use EcampCore\Repository\UserRepository;
use EcampLib\Service\ExecutionException;
use EcampLib\Service\ServiceBase;

class RegisterService
    extends ServiceBase
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

    public function __construct(
        UserRepository $userRepository,
        UserService $userService,
        LoginService $loginService
    ){
        $this->userRepository = $userRepository;
        $this->userService = $userService;
        $this->loginService = $loginService;
    }

    /**
     * @param $userInput
     * @return User
     * @throws \EcampLib\Service\ExecutionException
     */
    public function Register($userInput)
    {
        $user = $this->userService->Create($userInput['user-create']);

        if ($user->getState() != User::STATE_NONREGISTERED) {
            throw new ExecutionException("This eMail-Address is already registered!");
        }

        $user->setState(User::STATE_REGISTERED);
        $this->loginService->Create($user, $userInput['login-create']);

        SendActivationMailJob::Create($user);

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

}
