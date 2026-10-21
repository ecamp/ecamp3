<?php

declare(strict_types=1);

namespace App\Service\Hitobito;

class EventDate {
    public function __construct(
        public readonly string $id,
        public readonly ?string $label,
        public readonly string $startAt,
        public readonly ?string $finishAt,
    ) {}
}
