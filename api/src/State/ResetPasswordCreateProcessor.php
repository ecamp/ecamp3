<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\DTO\ResetPassword;
use App\Repository\UserRepository;
use App\Security\ReCaptcha\ReCaptcha;
use App\Service\MailService;
use App\Util\IdGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

class ResetPasswordCreateProcessor implements ProcessorInterface {
    public function __construct(
        private ReCaptcha $reCaptcha,
        private EntityManagerInterface $em,
        private UserRepository $userRepository,
        private PasswordHasherFactoryInterface $pwHasherFactory,
        private MailService $mailService
    ) {
    }

    /**
     * @param ResetPassword $data
     */
    public function process($data, Operation $operation, array $uriVariables = [], array $context = []): ResetPassword {
        $resp = $this->reCaptcha->verify($data->recaptchaToken);
        if (!$resp->isSuccess()) {
            throw new HttpException(422, 'ReCaptcha failed');
        }

        $user = $this->userRepository->loadUserByIdentifier($data->email);

        if (null == $user) {
            return $data;
        }

        $resetKey = IdGenerator::generateRandomHexString(64);

        $data->id = base64_encode($data->email.'#'.$resetKey);
        $user->passwordResetKeyHash = $this->getResetKeyHasher()->hash($resetKey);
        $this->em->flush();

        $this->mailService->sendPasswordResetLink($user, $data);

        return $data;
    }

    private function getResetKeyHasher(): PasswordHasherInterface {
        return $this->pwHasherFactory->getPasswordHasher('PasswordResetKey');
    }
}
