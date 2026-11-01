<?php

namespace App\Service\Image;

use App\Entity\ImageBlob;
use Symfony\Component\Filesystem\Filesystem;

final readonly class LocalImageStorage implements ImageStorage {
    public function __construct(private string $directory, private string $prefix) {}

    public function store(ProcessedImage $image): void {
        $key = ltrim(trim($this->prefix, '/').'/'.$image->getStorageKey(), '/');
        new Filesystem()->dumpFile(
            rtrim($this->directory, '/').'/'.$key,
            $image->data,
        );
    }

    public function getDeliveryHeaders(ImageBlob $image, bool $head = false): array {
        $key = ltrim(trim($this->prefix, '/').'/images/'.$image->sha256, '/');

        return ['X-Image-File' => rtrim($this->directory, '/').'/'.$key];
    }
}
