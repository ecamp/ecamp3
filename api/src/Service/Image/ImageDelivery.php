<?php

namespace App\Service\Image;

use App\Entity\CampImage;
use Aws\S3\S3ClientInterface;
use League\Flysystem\PathPrefixer;

final readonly class ImageDelivery {
    public function __construct(
        private string $driver,
        private string $directory,
        private string $prefix,
        private S3ClientInterface $client,
        private string $bucket,
    ) {}

    /** @return array<string, string> */
    public function getDeliveryHeaders(CampImage $image, bool $head = false): array {
        $key = new PathPrefixer($this->prefix)->prefixPath('images/'.$image->sha256);
        if ('local' === $this->driver) {
            return ['X-Image-File' => rtrim($this->directory, '/').'/'.$key];
        }
        if ('s3' === $this->driver) {
            $command = $this->client->getCommand($head ? 'HeadObject' : 'GetObject', [
                'Bucket' => $this->bucket,
                'Key' => $key,
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

        throw new \InvalidArgumentException('IMAGE_STORAGE must be local or s3.');
    }
}
