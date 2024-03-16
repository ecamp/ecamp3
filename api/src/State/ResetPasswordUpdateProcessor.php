<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\DTO\ResetPassword;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Security\ReCaptcha\ReCaptchaWrapper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

/**
 * @implements ProcessorInterface<ResetPassword,ResetPassword>
 */
class ResetPasswordUpdateProcessor implements ProcessorInterface {
    public function __construct(
        private ReCaptchaWrapper $reCaptcha,
        private EntityManagerInterface $em,
        private UserRepository $userRepository,
        private PasswordHasherFactoryInterface $pwHasherFactory,
    ) {}

    /**
     * @param ResetPassword $data
     */
    public function process($data, Operation $operation, array $uriVariables = [], array $context = []): ResetPassword {
        $resp = $this->reCaptcha->verify($data->recaptchaToken);
        if (!$resp->isSuccess()) {
            throw new HttpException(422, 'ReCaptcha failed');
        }

        [$email, $resetKey] = explode('#', base64_decode($data->id), 2);
        $user = $this->userRepository->loadUserByIdentifier($email);

        if (
            null == $user
            || null == $user->passwordResetKeyHash
            || !$this->getResetKeyHasher()->verify($user->passwordResetKeyHash, $resetKey)
        ) {
            throw new HttpException(422, 'Password reset failed');
        }

        $passwordHasher = $this->pwHasherFactory->getPasswordHasher($user);
        $user->password = $passwordHasher->hash($data->password);
        $user->passwordResetKeyHash = null;
        if (User::STATE_REGISTERED === $user->state) {
            $user->state = User::STATE_ACTIVATED;
        }
        $this->em->flush();

        $data->password = '';

        return $data;
    }

    private function getResetKeyHasher(): PasswordHasherInterface {
        return $this->pwHasherFactory->getPasswordHasher('PasswordResetKey');
    }
}
