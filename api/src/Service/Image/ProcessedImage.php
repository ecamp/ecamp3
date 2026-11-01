<?php

namespace App\Service\Image;

use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
final readonly class ProcessedImage {
    public string $sha256;

    public function __construct(public string $data, public int $width, public int $height) {
        $this->sha256 = hash('sha256', $data);
    }

    public function getStorageKey(): string {
        return 'images/'.$this->sha256;
    }
}
