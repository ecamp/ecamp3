<?php

declare(strict_types=1);

namespace App\Service\Hitobito;

class EventParticipation {
    /**
     * @param string[] $roleTypes
     */
    public function __construct(
        public readonly string $id,
        public readonly bool $active,
        public readonly string $eventId,
        public readonly array $roleTypes,
    ) {}
}
