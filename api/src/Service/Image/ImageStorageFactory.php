<?php

namespace App\Service\Image;

use Aws\S3\S3Client;

final class ImageStorageFactory {
    public function create(string $driver, LocalImageStorage $local, S3ImageStorage $s3): ImageStorage {
        return match ($driver) {
            'local' => $local,
            's3' => $s3,
            default => throw new \InvalidArgumentException('IMAGE_STORAGE must be local or s3.'),
        };
    }

    public function createS3Client(string $region, string $endpoint, bool $pathStyle): S3Client {
        $options = [
            'version' => '2006-03-01',
            'region' => $region,
            'use_path_style_endpoint' => $pathStyle,
        ];
        if ('' !== $endpoint) {
            $options['endpoint'] = $endpoint;
        }

        return new S3Client($options);
    }
}
