<?php

namespace App\Tests\Api\CampImages;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use App\Entity\CampImage;
use App\Entity\ImageBlob;
use App\Tests\Api\ECampApiTestCase;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @internal
 */
class UploadCampImageTest extends ECampApiTestCase {
    public function testAnonymousUploadIsDenied(): void {
        static::createBasicClient()->request('POST', $this->endpoint(), $this->options());
        self::assertResponseStatusCodeSame(401);
    }

    #[DataProvider('usersWithoutWriteAccess')]
    public function testUploadRequiresWriteAccess(string $user): void {
        static::createClientWithCredentials(['email' => static::$fixtures[$user]->getEmail()])->request('POST', $this->endpoint(), $this->options());
        self::assertResponseStatusCodeSame(403);
    }

    public static function usersWithoutWriteAccess(): iterable {
        yield 'guest' => ['user3guest'];

        yield 'unrelated' => ['user4unrelated'];

        yield 'inactive' => ['user5inactive'];
    }

    public function testMissingFileIsRejected(): void {
        static::createClientWithCredentials()->request('POST', $this->endpoint(), $this->options());
        self::assertResponseStatusCodeSame(400);
    }

    public function testUnknownCampIsNotFound(): void {
        static::createClientWithCredentials()->request('POST', '/camps/000000000000/images', $this->options());
        self::assertResponseStatusCodeSame(404);
    }

    public function testOpenApiDocumentsTheMultipartFile(): void {
        $openApi = static::getContainer()->get(OpenApiFactoryInterface::class)();
        $operation = $openApi->getPaths()->getPath('/camps/{campId}/images')->getPost();
        $schema = $operation->getRequestBody()->getContent()['multipart/form-data']['schema'];

        self::assertSame(['file'], $schema['required']);
        self::assertSame(['type' => 'string', 'format' => 'binary'], $schema['properties']['file']);
    }

    public function testInvalidImageIsRejectedWithoutCreatingRecords(): void {
        $client = static::createClientWithCredentials();
        $path = tempnam(sys_get_temp_dir(), 'image-test-');
        file_put_contents($path, 'not an image');

        try {
            $client->request('POST', $this->endpoint(), $this->options($path));
            self::assertResponseStatusCodeSame(422);
            self::assertSame(0, static::getContainer()->get(EntityManagerInterface::class)->getRepository(ImageBlob::class)->count([]));
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
            $first = $client->request('POST', $this->endpoint(), $this->options($path))->toArray();
            self::assertResponseStatusCodeSame(201);
            self::assertSame('image/webp', $first['mediaType']);
            self::assertSame(12, $first['width']);
            self::assertSame(6, $first['height']);

            $second = $client->request('POST', $this->endpoint(), $this->options($path))->toArray();
            self::assertResponseStatusCodeSame(201);
            self::assertSame($first['id'], $second['id']);
            self::assertSame($this->endpoint().'/'.$first['id'], $first['src']);

            $em = static::getContainer()->get(EntityManagerInterface::class);
            self::assertSame(1, $em->getRepository(ImageBlob::class)->count([]));
            self::assertSame(1, $em->getRepository(CampImage::class)->count([]));
        } finally {
            unlink($path);
        }
    }

    public function testTheSameFileInTwoCampsSharesTheBlobButNotTheCampImage(): void {
        $client = static::createClientWithCredentials();
        $client->disableReboot();
        $path = tempnam(sys_get_temp_dir(), 'image-test-');
        imagepng(imagecreatetruecolor(12, 6), $path);

        try {
            $first = $client->request('POST', $this->endpoint(), $this->options($path))->toArray();
            self::assertResponseStatusCodeSame(201);
            $second = $client->request('POST', '/camps/'.static::$fixtures['camp2']->getId().'/images', $this->options($path))->toArray();
            self::assertResponseStatusCodeSame(201);

            self::assertNotSame($first['id'], $second['id']);
            $em = static::getContainer()->get(EntityManagerInterface::class);
            self::assertSame(1, $em->getRepository(ImageBlob::class)->count([]));
            self::assertSame(2, $em->getRepository(CampImage::class)->count([]));
        } finally {
            unlink($path);
        }
    }

    private function endpoint(): string {
        return '/camps/'.static::$fixtures['camp1']->getId().'/images';
    }

    private function options(?string $path = null): array {
        return [
            'headers' => ['content-type' => 'multipart/form-data', 'accept' => 'application/json'],
            'extra' => ['files' => null === $path ? [] : ['file' => new UploadedFile($path, 'image.png', null, null, true)]],
        ];
    }
}
