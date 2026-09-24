<?php

declare(strict_types=1);

namespace App\Service\Hitobito;

class EventParticipant {
    public function __construct(
        public readonly ?string $firstName,
        public readonly ?string $lastName,
        public readonly ?string $nickname,
        public readonly string $email,
    ) {}
}
