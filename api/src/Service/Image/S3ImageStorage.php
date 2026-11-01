<?php

namespace App\Service\Image;

use Aws\S3\S3ClientInterface;

final readonly class S3ImageStorage implements ImageStorage {
    public function __construct(private S3ClientInterface $client, private string $bucket, private string $prefix) {}

    public function store(ProcessedImage $image): void {
        if ('' === $this->bucket) {
            throw new \LogicException('IMAGE_STORAGE_BUCKET must be configured for S3 storage.');
        }

        $this->client->putObject([
            'Bucket' => $this->bucket,
            'Key' => ltrim(trim($this->prefix, '/').'/'.$image->getStorageKey(), '/'),
            'Body' => $image->data,
            'ContentType' => 'image/webp',
        ]);
    }
}
