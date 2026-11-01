<?php

namespace App\Tests\Service\Image;

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
