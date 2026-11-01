<?php

namespace App\Tests\Service\Image;

use App\Entity\ImageBlob;
use App\Service\Image\LocalImageStorage;
use App\Service\Image\ProcessedImage;
use App\Service\Image\S3ImageStorage;
use Aws\MockHandler;
use Aws\Result;
use Aws\S3\S3Client;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @internal
 */
class ImageStorageTest extends TestCase {
    public function testS3DeliveryUsesAShortLivedSignedUrlWithoutDownloading(): void {
        $client = new S3Client([
            'version' => '2006-03-01',
            'region' => 'eu-central-1',
            'endpoint' => 'https://storage.example.com',
            'use_path_style_endpoint' => true,
            'credentials' => ['key' => 'test', 'secret' => 'test'],
            'handler' => new MockHandler(),
        ]);
        $blob = new ImageBlob();
        $blob->sha256 = hash('sha256', 'image');
        $headers = new S3ImageStorage($client, 'preview', 'branch/')->getDeliveryHeaders($blob);

        self::assertSame('https', $headers['X-Image-Scheme']);
        self::assertSame('storage.example.com:443', $headers['X-Image-Upstream']);
        self::assertStringStartsWith('/preview/branch/images/'.$blob->sha256.'?', $headers['X-Image-Uri']);
        parse_str(parse_url($headers['X-Image-Uri'], PHP_URL_QUERY), $query);
        self::assertSame('60', $query['X-Amz-Expires']);
        self::assertNotEmpty($query['X-Amz-Signature']);
    }

    public function testLocalStorageUsesThePrefixAndCanStoreTheSameImageTwice(): void {
        $directory = sys_get_temp_dir().'/image-storage-'.bin2hex(random_bytes(8));
        $image = new ProcessedImage('image bytes', 12, 6);

        try {
            $storage = new LocalImageStorage($directory, 'deployments/test/');
            $storage->store($image);
            $storage->store($image);

            self::assertSame($image->data, file_get_contents($directory.'/deployments/test/images/'.$image->sha256));
        } finally {
            new Filesystem()->remove($directory);
        }
    }

    public function testS3StorageUploadsToTheConfiguredBucketAndPrefix(): void {
        $handler = new MockHandler([new Result()]);
        $client = new S3Client([
            'version' => '2006-03-01',
            'region' => 'eu-central-1',
            'credentials' => ['key' => 'test', 'secret' => 'test'],
            'handler' => $handler,
        ]);
        $image = new ProcessedImage('image bytes', 12, 6);

        new S3ImageStorage($client, 'preview', 'deployments/test/')->store($image);

        $command = $handler->getLastCommand();
        self::assertSame('PutObject', $command->getName());
        self::assertSame('preview', $command['Bucket']);
        self::assertSame('deployments/test/images/'.$image->sha256, $command['Key']);
        self::assertSame($image->data, $command['Body']);
        self::assertSame('image/webp', $command['ContentType']);
        self::assertNull($command['ACL']);
    }
}
