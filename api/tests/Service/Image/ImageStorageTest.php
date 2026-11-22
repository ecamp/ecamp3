<?php

namespace App\Tests\Service\Image;

use App\Entity\CampImage;
use App\Kernel;
use App\Service\Image\ImageDelivery;
use App\Service\Image\ProcessedImage;
use App\Service\Image\S3ClientFactory;
use Aws\Command;
use Aws\Exception\AwsException;
use Aws\MockHandler;
use Aws\Result;
use Aws\S3\S3Client;
use Aws\S3\S3ClientInterface;
use GuzzleHttp\Psr7\Request;
use League\Flysystem\Filesystem as Flysystem;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\UnableToDeleteFile;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @internal
 */
class ImageStorageTest extends TestCase {
    private ?Kernel $kernel = null;

    private array $previousServer = [];
    private array $previousEnv = [];

    protected function tearDown(): void {
        $this->kernel?->shutdown();
        foreach ($this->previousServer as $name => $value) {
            if (null === $value) {
                unset($_SERVER[$name]);
            } else {
                $_SERVER[$name] = $value;
            }
        }
        foreach ($this->previousEnv as $name => $value) {
            if (null === $value) {
                unset($_ENV[$name]);
            } else {
                $_ENV[$name] = $value;
            }
        }
        parent::tearDown();
    }

    public function testLocalDeletionIsRepeatableAndUsesTheConfiguredPrefix(): void {
        $directory = sys_get_temp_dir().'/image-storage-'.bin2hex(random_bytes(8));
        $storage = $this->bootStorage([
            'IMAGE_STORAGE' => 'local',
            'IMAGE_STORAGE_PATH' => $directory,
            'IMAGE_STORAGE_PREFIX' => 'branch/',
        ]);
        $image = new ProcessedImage('delete image', 12, 6);

        try {
            $storage->write($image->getStorageKey(), $image->data);
            $storage->delete($image->getStorageKey());
            $storage->delete($image->getStorageKey());
            self::assertFileDoesNotExist($directory.'/branch/images/'.$image->sha256);
        } finally {
            new Filesystem()->remove($directory);
        }
    }

    public function testS3DeletionUsesTheConfiguredBucketAndPrefix(): void {
        $handler = new MockHandler([new Result()]);
        $client = new S3Client([
            'version' => '2006-03-01',
            'region' => 'eu-central-1',
            'credentials' => ['key' => 'test', 'secret' => 'test'],
            'handler' => $handler,
        ]);
        $sha256 = hash('sha256', 'delete image');

        $this->bootStorage([
            'IMAGE_STORAGE' => 's3',
            'IMAGE_STORAGE_BUCKET' => 'preview',
            'IMAGE_STORAGE_PREFIX' => 'branch/',
        ], $client)->delete('images/'.$sha256);

        $command = $handler->getLastCommand();
        self::assertSame('DeleteObject', $command->getName());
        self::assertSame('preview', $command['Bucket']);
        self::assertSame('branch/images/'.$sha256, $command['Key']);
    }

    public function testS3DeliveryUsesAShortLivedSignedUrlWithoutDownloading(): void {
        $client = new S3Client([
            'version' => '2006-03-01',
            'region' => 'eu-central-1',
            'endpoint' => 'https://storage.example.com',
            'use_path_style_endpoint' => true,
            'credentials' => ['key' => 'test', 'secret' => 'test'],
            'handler' => new MockHandler(),
        ]);
        $image = new CampImage();
        $image->sha256 = hash('sha256', 'image');
        $headers = new ImageDelivery('s3', '', 'branch/', $client, 'preview')->getDeliveryHeaders($image);

        self::assertSame('https', $headers['X-Image-Scheme']);
        self::assertSame('storage.example.com:443', $headers['X-Image-Upstream']);
        self::assertStringStartsWith('/preview/branch/images/'.$image->sha256.'?', $headers['X-Image-Uri']);
        parse_str(parse_url($headers['X-Image-Uri'], PHP_URL_QUERY), $query);
        self::assertSame('60', $query['X-Amz-Expires']);
        self::assertNotEmpty($query['X-Amz-Signature']);
    }

    public function testLocalStorageUsesThePrefixAndCanStoreTheSameImageTwice(): void {
        $directory = sys_get_temp_dir().'/image-storage-'.bin2hex(random_bytes(8));
        $image = new ProcessedImage('image bytes', 12, 6);

        try {
            $storage = $this->bootStorage([
                'IMAGE_STORAGE' => 'local',
                'IMAGE_STORAGE_PATH' => $directory,
                'IMAGE_STORAGE_PREFIX' => 'deployments/test/',
            ]);
            $storage->write($image->getStorageKey(), $image->data);
            $storage->write($image->getStorageKey(), $image->data);

            self::assertSame($image->data, file_get_contents($directory.'/deployments/test/images/'.$image->sha256));
        } finally {
            new Filesystem()->remove($directory);
        }
    }

    public function testS3StorageUploadsToTheConfiguredBucketAndPrefix(): void {
        $handler = new MockHandler([new Result()]);
        $client = $this->createS3Client($handler);
        $image = new ProcessedImage('image bytes', 12, 6);

        $storage = $this->bootStorage([
            'IMAGE_STORAGE' => 's3',
            'IMAGE_STORAGE_BUCKET' => 'preview',
            'IMAGE_STORAGE_PREFIX' => 'deployments/test/',
        ], $client);
        $storage->write($image->getStorageKey(), $image->data, ['mimetype' => ProcessedImage::MEDIA_TYPE]);

        $command = $handler->getLastCommand();
        self::assertSame('PutObject', $command->getName());
        self::assertSame('preview', $command['Bucket']);
        self::assertSame('deployments/test/images/'.$image->sha256, $command['Key']);
        self::assertSame($image->data, (string) $command['Body']);
        self::assertSame('image/avif', $command['ContentType']);
        self::assertNull($command['ACL']);

        $campImage = new CampImage();
        $campImage->sha256 = $image->sha256;
        $delivery = $this->kernel->getContainer()->get('test.service_container')->get(ImageDelivery::class);
        $headers = $delivery->getDeliveryHeaders($campImage);
        self::assertSame('/'.$command['Bucket'].'/'.$command['Key'], parse_url($headers['X-Image-Uri'], PHP_URL_PATH));
    }

    public function testLocalDeliveryUsesTheSamePathAsTheConfiguredStorage(): void {
        $directory = sys_get_temp_dir().'/image-storage-'.bin2hex(random_bytes(8));
        $processed = new ProcessedImage('local delivery', 12, 6);
        $image = new CampImage();
        $image->sha256 = $processed->sha256;
        $storage = $this->bootStorage([
            'IMAGE_STORAGE' => 'local',
            'IMAGE_STORAGE_PATH' => $directory.'/',
            'IMAGE_STORAGE_PREFIX' => 'branch/',
        ]);

        try {
            $storage->write($processed->getStorageKey(), $processed->data);
            $delivery = $this->kernel->getContainer()->get('test.service_container')->get(ImageDelivery::class);
            $headers = $delivery->getDeliveryHeaders($image);

            self::assertSame(['X-Image-File' => $directory.'/branch/images/'.$image->sha256], $headers);
            self::assertSame($processed->data, file_get_contents($headers['X-Image-File']));
        } finally {
            new Filesystem()->remove($directory);
        }
    }

    public function testS3HeadDeliverySignsTheHeadMethod(): void {
        $client = $this->createMock(S3ClientInterface::class);
        $image = new CampImage();
        $image->sha256 = hash('sha256', 'head delivery');
        $command = new Command('HeadObject');
        $client->expects(self::once())->method('getCommand')->with('HeadObject', [
            'Bucket' => 'preview',
            'Key' => 'branch/images/'.$image->sha256,
        ])->willReturn($command);
        $client->expects(self::once())->method('createPresignedRequest')->with($command, '+1 minute')
            ->willReturn(new Request('HEAD', 'https://storage.example.com/image?signature=head'))
        ;

        $headers = new ImageDelivery('s3', '', 'branch/', $client, 'preview')->getDeliveryHeaders($image, true);

        self::assertSame('/image?signature=head', $headers['X-Image-Uri']);
    }

    #[DataProvider('storageDrivers')]
    public function testStorageSelectionUsesTheConfiguredAdapter(string $driver): void {
        $storage = $this->bootStorage(['IMAGE_STORAGE' => $driver]);
        self::assertInstanceOf(Flysystem::class, $storage);
        self::assertSame($this->kernel->getContainer()->get('test.service_container')->get('image.storage.'.$driver), $storage);
    }

    public static function storageDrivers(): iterable {
        yield 'local' => ['local'];

        yield 'S3' => ['s3'];
    }

    public function testS3DeletionFailureIsReported(): void {
        $handler = new MockHandler([new AwsException('Storage unavailable', new Command('DeleteObject'))]);
        $client = $this->createS3Client($handler);
        $storage = $this->bootStorage([
            'IMAGE_STORAGE' => 's3',
            'IMAGE_STORAGE_BUCKET' => 'preview',
            'IMAGE_STORAGE_PREFIX' => 'branch/',
        ], $client);

        $this->expectException(UnableToDeleteFile::class);
        $storage->delete('images/'.hash('sha256', 'delete failure'));
    }

    /** @param array<string, string> $environment */
    private function bootStorage(array $environment, ?S3ClientInterface $client = null): FilesystemOperator {
        foreach ($environment as $name => $value) {
            $this->previousServer[$name] = $_SERVER[$name] ?? null;
            $this->previousEnv[$name] = $_ENV[$name] ?? null;
            $_SERVER[$name] = $_ENV[$name] = $value;
        }
        $this->kernel = new Kernel('test', true);
        $this->kernel->boot();
        $container = $this->kernel->getContainer()->get('test.service_container');
        if (null !== $client) {
            $container->set(S3ClientInterface::class, $client);
        }

        return $container->get(FilesystemOperator::class);
    }

    private function createS3Client(MockHandler $handler): S3Client {
        return new S3ClientFactory()->create([
            'version' => '2006-03-01',
            'region' => 'eu-central-1',
            'endpoint' => 'https://storage.example.com',
            'use_path_style_endpoint' => true,
            'credentials' => ['key' => 'test', 'secret' => 'test'],
            'handler' => $handler,
        ]);
    }
}
