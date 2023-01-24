<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use App\Security\ReCaptcha\ReCaptcha;
use App\Service\MailService;
use App\State\Util\AbstractPersistProcessor;
use App\Util\IdGenerator;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserCreateProcessor extends AbstractPersistProcessor {
    public function __construct(
        ProcessorInterface $decorated,
        private ReCaptcha $reCaptcha,
        private UserPasswordHasherInterface $userPasswordHasher,
        private MailService $mailService
    ) {
        parent::__construct($decorated);
    }

    /**
     * @param User $data
     */
    public function onBefore($data, Operation $operation, array $uriVariables = [], array $context = []): User {
        $resp = $this->reCaptcha->verify($data->recaptchaToken);
        if (!$resp->isSuccess()) {
            throw new HttpException(422, 'ReCaptcha failed');
        }

        $data->state = User::STATE_REGISTERED;
        if ($data->plainPassword) {
            $data->password = $this->userPasswordHasher->hashPassword($data, $data->plainPassword);
            $data->eraseCredentials();
        }
        $data->activationKey = IdGenerator::generateRandomHexString(64);
        $data->activationKeyHash = md5($data->activationKey);

        return $data;
    }

    /**
     * @param User $data
     */
    public function onAfter($data, Operation $operation, array $uriVariables = [], array $context = []): void {
        $this->mailService->sendUserActivationMail($data, $data->activationKey);
    }
}
