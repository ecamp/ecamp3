<?php

declare(strict_types=1);

namespace App\DTO;

use ApiPlatform\Metadata\ApiProperty;
use Symfony\Component\Serializer\Attribute\Groups;

class HitobitoEventDate {
    #[ApiProperty(example: '5678')]
    #[Groups(['details'])]
    public int $id;

    #[Groups(['details'])]
    public ?string $label = null;

    #[Groups(['details'])]
    public string $startAt;

    #[Groups(['details'])]
    public ?string $finishAt = null;

    public function __construct(string $id, ?string $label, string $startAt, ?string $finishAt) {
        $this->id = intval($id);
        // these fields may be an empty string at Hitobito, normalize to null
        if ('' != $label) {
            $this->label = $label;
        }
        $this->startAt = $startAt;

        $this->finishAt = $finishAt;
    }
}
