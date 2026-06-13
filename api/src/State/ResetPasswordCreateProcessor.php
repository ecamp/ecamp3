<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\DTO\ResetPassword;
use App\Repository\UserRepository;
use App\Security\ReCaptcha\ReCaptchaWrapper;
use App\Service\MailService;
use App\Util\IdGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

/**
 * @implements ProcessorInterface<ResetPassword,null>
 */
class ResetPasswordCreateProcessor implements ProcessorInterface {
    public function __construct(
        private readonly ReCaptchaWrapper $reCaptcha,
        private readonly EntityManagerInterface $em,
        private readonly UserRepository $userRepository,
        private readonly PasswordHasherFactoryInterface $pwHasherFactory,
        private readonly MailService $mailService
    ) {}

    /**
     * @param ResetPassword $data
     */
    #[\Override]
    public function process($data, Operation $operation, array $uriVariables = [], array $context = []): null {
        $resp = $this->reCaptcha->verify($data->recaptchaToken);
        if (!$resp->isSuccess()) {
            throw new HttpException(422, 'ReCaptcha failed');
        }

        $user = $this->userRepository->loadUserByIdentifier($data->email);

        if (null == $user) {
            return null;
        }

        $resetKey = IdGenerator::generateRandomHexString(64);

        $data->id = base64_encode($data->email.'#'.$resetKey);
        $user->passwordResetKeyHash = $this->getResetKeyHasher()->hash($resetKey);
        $this->em->flush();

        $this->mailService->sendPasswordResetLink($user, $data);

        return null;
    }

    private function getResetKeyHasher(): PasswordHasherInterface {
        return $this->pwHasherFactory->getPasswordHasher('PasswordResetKey');
    }
}
