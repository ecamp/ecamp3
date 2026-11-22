<?php

namespace App\Tests\Api\CampImages;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use App\Entity\CampImage;
use App\Tests\Api\ECampApiTestCase;
use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\FilesystemOperator;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @internal
 */
class UploadCampImageTest extends ECampApiTestCase {
    public function testAnonymousUploadIsDenied(): void {
        static::createBasicClient()->request('POST', '/images', $this->options());
        self::assertResponseStatusCodeSame(401);
    }

    #[DataProvider('usersWithoutWriteAccess')]
    public function testUploadRequiresWriteAccess(string $user): void {
        static::createClientWithCredentials(['email' => static::$fixtures[$user]->getEmail()])->request('POST', '/images', $this->options());
        self::assertResponseStatusCodeSame(403);
    }

    public static function usersWithoutWriteAccess(): iterable {
        yield 'guest' => ['user3guest'];

        yield 'unrelated' => ['user4unrelated'];

        yield 'inactive' => ['user5inactive'];
    }

    public function testMissingFileIsRejected(): void {
        static::createClientWithCredentials()->request('POST', '/images', $this->options());
        self::assertResponseStatusCodeSame(400);
    }

    #[DataProvider('invalidCampFields')]
    public function testInvalidCampFieldIsRejected(array $parameters): void {
        static::createClientWithCredentials()->request('POST', '/images', $this->options(parameters: $parameters));
        self::assertResponseStatusCodeSame(400);
    }

    public static function invalidCampFields(): iterable {
        yield 'missing' => [[]];

        yield 'malformed' => [['campId' => '/camps/123456789abc']];

        yield 'array' => [['campId' => ['123456789abc']]];
    }

    public function testUnknownCampIsNotFound(): void {
        static::createClientWithCredentials()->request('POST', '/images', $this->options(parameters: ['campId' => '000000000000']));
        self::assertResponseStatusCodeSame(404);
    }

    public function testOpenApiDocumentsTheMultipartFile(): void {
        $openApi = static::getContainer()->get(OpenApiFactoryInterface::class)();
        $operation = $openApi->getPaths()->getPath('/images')->getPost();
        $schema = $operation->getRequestBody()->getContent()['multipart/form-data']['schema'];

        self::assertSame(['campId', 'file'], $schema['required']);
        self::assertSame(['type' => 'string', 'pattern' => '^[0-9a-f]{12,16}$'], $schema['properties']['campId']);
        self::assertSame(['type' => 'string', 'format' => 'binary'], $schema['properties']['file']);
    }

    public function testInvalidImageIsRejectedWithoutCreatingRecords(): void {
        $client = static::createClientWithCredentials();
        $path = tempnam(sys_get_temp_dir(), 'image-test-');
        file_put_contents($path, 'not an image');

        try {
            $client->request('POST', '/images', $this->options($path));
            self::assertResponseStatusCodeSame(422);
            self::assertSame(0, static::getContainer()->get(EntityManagerInterface::class)->getRepository(CampImage::class)->count([]));
        } finally {
            unlink($path);
        }
    }

    public function testMemberCanUploadAndRepeatedUploadsReuseTheImage(): void {
        $client = static::createClientWithCredentials(['email' => static::$fixtures['user2member']->getEmail()]);
        $client->disableReboot();
        $path = tempnam(sys_get_temp_dir(), 'image-test-');
        imagepng(imagecreatetruecolor(12, 6), $path);

        try {
            $options = $this->options($path);
            $options['headers']['X-Forwarded-Prefix'] = '/api';
            $first = $client->request('POST', '/images', $options)->toArray();
            self::assertResponseStatusCodeSame(201);
            self::assertSame('image/avif', $first['mediaType']);
            self::assertSame(12, $first['width']);
            self::assertSame(6, $first['height']);

            $second = $client->request('POST', '/images', $options)->toArray();
            self::assertResponseStatusCodeSame(201);
            self::assertSame($first['id'], $second['id']);
            self::assertSame($first['src'], $second['src']);

            $em = static::getContainer()->get(EntityManagerInterface::class);
            self::assertSame(1, $em->getRepository(CampImage::class)->count([]));
            $image = $em->find(CampImage::class, $first['id']);
            self::assertSame('image/avif', $image->mediaType);
            self::assertSame('/api/images/'.$image->sha256.'/content', $first['src']);
        } finally {
            unlink($path);
        }
    }

    public function testTheSameFileInTwoCampsSharesTheHash(): void {
        $client = static::createClientWithCredentials();
        $client->disableReboot();
        $path = tempnam(sys_get_temp_dir(), 'image-test-');
        imagepng(imagecreatetruecolor(12, 6), $path);

        try {
            $first = $client->request('POST', '/images', $this->options($path))->toArray();
            self::assertResponseStatusCodeSame(201);
            $second = $client->request('POST', '/images', $this->options($path, ['campId' => static::$fixtures['camp2']->getId()]))->toArray();
            self::assertResponseStatusCodeSame(201);

            self::assertNotSame($first['id'], $second['id']);
            self::assertSame($first['src'], $second['src']);
            $em = static::getContainer()->get(EntityManagerInterface::class);
            self::assertSame($em->find(CampImage::class, $first['id'])->sha256, $em->find(CampImage::class, $second['id'])->sha256);
            self::assertSame(2, $em->getRepository(CampImage::class)->count([]));
        } finally {
            unlink($path);
        }
    }

    public function testSvgUploadIsStoredAsAvif(): void {
        $client = static::createClientWithCredentials();
        $path = tempnam(sys_get_temp_dir(), 'image-test-');
        file_put_contents($path, '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 120 60"><rect width="120" height="60" fill="red"/></svg>');

        try {
            $response = $client->request('POST', '/images', $this->options($path))->toArray();
            self::assertResponseStatusCodeSame(201);
            self::assertSame('image/avif', $response['mediaType']);
            $image = static::getContainer()->get(EntityManagerInterface::class)->find(CampImage::class, $response['id']);
            $bytes = static::getContainer()->get(FilesystemOperator::class)->read('images/'.$image->sha256);
            $info = getimagesizefromstring($bytes);
            self::assertSame(IMAGETYPE_AVIF, $info[2]);
            self::assertSame($response['width'], $info[0]);
            self::assertSame($response['height'], $info[1]);
            self::assertSame($response['width'], 2 * $response['height']);
        } finally {
            unlink($path);
        }
    }

    private function options(?string $path = null, ?array $parameters = null): array {
        return [
            'headers' => ['content-type' => 'multipart/form-data', 'accept' => 'application/json'],
            'extra' => [
                'parameters' => $parameters ?? ['campId' => static::$fixtures['camp1']->getId()],
                'files' => null === $path ? [] : ['file' => new UploadedFile($path, 'image.png', null, null, true)],
            ],
        ];
    }
}
