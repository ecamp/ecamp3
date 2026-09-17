<?php

namespace App\Service\Hitobito;

class AuthContext {
    public function __construct(
        private readonly int $userId,
        private readonly string $accessToken
    ) {}

    public function getUserId(): int {
        return $this->userId;
    }

    public function getAccessToken(): string {
        return $this->accessToken;
    }
}
