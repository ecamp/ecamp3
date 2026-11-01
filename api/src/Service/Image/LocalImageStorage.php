<?php

namespace App\Service\Image;

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
}
