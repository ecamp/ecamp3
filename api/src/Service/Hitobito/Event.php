<?php

declare(strict_types=1);

namespace App\Service\Hitobito;

class Event {
    /**
     * @param EventDate[] $dates
     */
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly ?string $motto,
        public readonly ?string $location,
        public readonly array $dates,
    ) {}
}
