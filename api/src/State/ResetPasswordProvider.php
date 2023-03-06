<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\DTO\ResetPassword;
use App\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

/**
 * @template-implements ProviderInterface<ResetPassword>
 */
class ResetPasswordProvider implements ProviderInterface {
    public function __construct(
        private UserRepository $userRepository,
        private PasswordHasherFactoryInterface $pwHasherFactory,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?ResetPassword {
        $id = $uriVariables['id'];

        if (null == $id) {
            return null;
        }

        // load user
        [$email, $resetKey] = explode('#', base64_decode($id), 2);
        $user = $this->userRepository->loadUserByIdentifier($email);

        if (null == $user) {
            return null;
        }
        // if user has no passwordResetKeyHash -> 404
        if (null == $user->passwordResetKeyHash) {
            return null;
        }
        // if no resetKey -> 404
        if (null == $resetKey) {
            return null;
        }
        // if resetKey does not match -> 404
        if (!$this->getResetKeyHasher()->verify($user->passwordResetKeyHash, $resetKey)) {
            return null;
        }

        $resetPassword = new ResetPassword();
        $resetPassword->id = $id;
        $resetPassword->email = $email;

        return $resetPassword;
    }

    private function getResetKeyHasher(): PasswordHasherInterface {
        return $this->pwHasherFactory->getPasswordHasher('PasswordResetKey');
    }
}
