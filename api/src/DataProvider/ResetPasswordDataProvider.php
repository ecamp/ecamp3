<?php

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\DTO\ResetPassword;
use App\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

class ResetPasswordDataProvider implements ItemDataProviderInterface, RestrictedDataProviderInterface {
    public function __construct(
        private UserRepository $userRepository,
        private PasswordHasherFactoryInterface $pwHasherFactory,
    ) {
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = []): ?ResetPassword {
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

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool {
        return ResetPassword::class === $resourceClass;
    }

    private function getResetKeyHasher(): PasswordHasherInterface {
        return $this->pwHasherFactory->getPasswordHasher('PasswordResetKey');
    }
}
