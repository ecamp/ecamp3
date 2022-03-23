<?php

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\DTO\ResetPassword;

class ResetPasswordDataProvider implements ItemDataProviderInterface, RestrictedDataProviderInterface {
    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = []): ?ResetPassword {
        if (null == $id) {
            return null;
        }

        $resetPassword = new ResetPassword();
        $resetPassword->emailBase64 = $id;

        return $resetPassword;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool {
        return ResetPassword::class === $resourceClass;
    }
}
