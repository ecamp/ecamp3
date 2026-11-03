<?php

namespace App\Service\Image;

use App\Entity\ImageBlob;
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

    public function delete(ImageBlob $image): void {
        $this->client->deleteObject([
            'Bucket' => $this->bucket,
            'Key' => ltrim(trim($this->prefix, '/').'/images/'.$image->sha256, '/'),
        ]);
    }

    public function getDeliveryHeaders(ImageBlob $image, bool $head = false): array {
        $command = $this->client->getCommand($head ? 'HeadObject' : 'GetObject', [
            'Bucket' => $this->bucket,
            'Key' => ltrim(trim($this->prefix, '/').'/images/'.$image->sha256, '/'),
        ]);
        $uri = $this->client->createPresignedRequest($command, '+1 minute')->getUri();

        return [
            'X-Image-Scheme' => $uri->getScheme(),
            'X-Image-Upstream' => $uri->getAuthority().(null === $uri->getPort() ? (('https' === $uri->getScheme()) ? ':443' : ':80') : ''),
            'X-Image-Host' => $uri->getAuthority(),
            'X-Image-Server-Name' => $uri->getHost(),
            'X-Image-Uri' => $uri->getPath().'?'.$uri->getQuery(),
        ];
    }
}
