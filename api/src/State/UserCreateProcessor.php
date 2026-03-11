<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use App\Security\ReCaptcha\ReCaptchaWrapper;
use App\Service\MailService;
use App\State\Util\AbstractPersistProcessor;
use App\Util\IdGenerator;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @template-extends AbstractPersistProcessor<User>
 */
class UserCreateProcessor extends AbstractPersistProcessor {
    public function __construct(
        ProcessorInterface $decorated,
        private readonly ReCaptchaWrapper $reCaptcha,
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private readonly MailService $mailService
    ) {
        parent::__construct($decorated);
    }

    /**
     * @param User $data
     */
    #[\Override]
    public function onBefore($data, Operation $operation, array $uriVariables = [], array $context = []): User {
        $resp = $this->reCaptcha->verify($data->recaptchaToken);
        if (!$resp->isSuccess()) {
            throw new HttpException(422, 'ReCaptcha failed');
        }

        $data->state = User::STATE_REGISTERED;
        if ($data->plainPassword) {
            $data->password = $this->userPasswordHasher->hashPassword($data, $data->plainPassword);
            $data->prepareForSerialization();
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
