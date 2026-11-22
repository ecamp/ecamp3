<?php

namespace App\Tests\Api\CampImages;

use App\Entity\Camp;
use App\Entity\CampImage;
use App\Tests\Api\ECampApiTestCase;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * @internal
 */
class ReadCampImageTest extends ECampApiTestCase {
    private CampImage $image;

    public function setUp(): void {
        parent::setUp();
        $em = static::getContainer()->get(EntityManagerInterface::class);
        $this->image = new CampImage();
        $this->image->camp = $em->find(Camp::class, static::$fixtures['camp1']->getId());
        $this->image->sha256 = hash('sha256', 'test image');
        $this->image->mediaType = 'image/avif';
        $em->persist($this->image);
        $em->flush();
    }

    public function testGuestCanReadAndPhpOnlyReturnsDeliveryHeaders(): void {
        $response = static::createClientWithCredentials(['email' => static::$fixtures['user3guest']->getEmail()])->request('GET', $this->url(), ['headers' => ['accept' => 'image/avif']]);
        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('ETag', '"'.$this->image->sha256.'"');
        self::assertResponseHeaderSame('Cache-Control', 'no-cache, private');
        self::assertResponseHasHeader('X-Image-File');
        self::assertSame('', $response->getContent());
    }

    public function testMatchingEtagReturns304WithoutDeliveryHeaders(): void {
        static::createClientWithCredentials()->request('GET', $this->url(), ['headers' => [
            'accept' => 'image/avif',
            'if-none-match' => '"'.$this->image->sha256.'"',
        ]]);
        self::assertResponseStatusCodeSame(304);
        self::assertResponseNotHasHeader('X-Image-File');
    }

    public function testDifferentEtagStillDeliversTheImage(): void {
        static::createClientWithCredentials()->request('GET', $this->url(), ['headers' => [
            'accept' => 'image/avif',
            'if-none-match' => '"old-image"',
        ]]);
        self::assertResponseStatusCodeSame(200);
        self::assertResponseHasHeader('X-Image-File');
    }

    public function testHeadChecksPermissionsAndReturnsTheEtag(): void {
        static::createClientWithCredentials()->request('HEAD', $this->url(), ['headers' => ['accept' => 'image/avif']]);
        self::assertResponseStatusCodeSame(200);
        self::assertResponseHeaderSame('ETag', '"'.$this->image->sha256.'"');
    }

    #[DataProvider('unauthorizedUsers')]
    public function testMatchingEtagDoesNotBypassPermissions(?string $user): void {
        $client = null === $user ? static::createBasicClient() : static::createClientWithCredentials(['email' => static::$fixtures[$user]->getEmail()]);
        $client->request('GET', $this->url(), ['headers' => [
            'accept' => 'image/avif',
            'if-none-match' => '"'.$this->image->sha256.'"',
        ]]);
        self::assertResponseStatusCodeSame(null === $user ? 401 : 403);
        self::assertResponseNotHasHeader('X-Image-File');
    }

    public static function unauthorizedUsers(): iterable {
        yield 'anonymous' => [null];

        yield 'unrelated' => ['user4unrelated'];

        yield 'inactive' => ['user5inactive'];
    }

    public function testUnknownHashIsNotFound(): void {
        static::createClientWithCredentials()->request('GET', '/images/'.str_repeat('0', 64).'/content', ['headers' => ['accept' => 'image/avif']]);
        self::assertResponseStatusCodeSame(404);
    }

    public function testSharedCampImageCanBeReadAnonymously(): void {
        $em = static::getContainer()->get(EntityManagerInterface::class);
        $sharedImage = new CampImage();
        $sharedImage->camp = $em->find(Camp::class, static::$fixtures['campShared']->getId());
        $sharedImage->sha256 = $this->image->sha256;
        $sharedImage->mediaType = $this->image->mediaType;
        $em->persist($sharedImage);
        $em->flush();
        static::createBasicClient()->request('GET', $this->url(), ['headers' => ['accept' => 'image/avif']]);
        self::assertResponseIsSuccessful();
    }

    private function url(): string {
        return '/images/'.$this->image->sha256.'/content';
    }
}
