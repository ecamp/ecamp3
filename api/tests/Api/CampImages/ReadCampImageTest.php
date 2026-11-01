<?php

namespace App\Tests\Api\CampImages;

use App\Entity\Camp;
use App\Entity\CampImage;
use App\Entity\ImageBlob;
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
        $blob = new ImageBlob();
        $blob->sha256 = hash('sha256', 'test image');
        $blob->mediaType = 'image/webp';
        $blob->byteSize = 10;
        $blob->width = 12;
        $blob->height = 6;
        $this->image = new CampImage();
        $this->image->camp = $em->find(Camp::class, static::$fixtures['camp1']->getId());
        $this->image->imageBlob = $blob;
        $em->persist($blob);
        $em->persist($this->image);
        $em->flush();
    }

    public function testGuestCanReadAndPhpOnlyReturnsDeliveryHeaders(): void {
        $response = static::createClientWithCredentials(['email' => static::$fixtures['user3guest']->getEmail()])->request('GET', $this->url(), ['headers' => ['accept' => 'image/webp']]);
        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('ETag', '"'.$this->image->imageBlob->sha256.'"');
        self::assertResponseHeaderSame('Cache-Control', 'no-cache, private');
        self::assertResponseHasHeader('X-Image-File');
        self::assertSame('', $response->getContent());
    }

    public function testMatchingEtagReturns304WithoutDeliveryHeaders(): void {
        static::createClientWithCredentials()->request('GET', $this->url(), ['headers' => [
            'accept' => 'image/webp',
            'if-none-match' => '"'.$this->image->imageBlob->sha256.'"',
        ]]);
        self::assertResponseStatusCodeSame(304);
        self::assertResponseNotHasHeader('X-Image-File');
    }

    public function testDifferentEtagStillDeliversTheImage(): void {
        static::createClientWithCredentials()->request('GET', $this->url(), ['headers' => [
            'accept' => 'image/webp',
            'if-none-match' => '"old-image"',
        ]]);
        self::assertResponseStatusCodeSame(200);
        self::assertResponseHasHeader('X-Image-File');
    }

    public function testHeadChecksPermissionsAndReturnsTheEtag(): void {
        static::createClientWithCredentials()->request('HEAD', $this->url(), ['headers' => ['accept' => 'image/webp']]);
        self::assertResponseStatusCodeSame(200);
        self::assertResponseHeaderSame('ETag', '"'.$this->image->imageBlob->sha256.'"');
    }

    #[DataProvider('unauthorizedUsers')]
    public function testMatchingEtagDoesNotBypassPermissions(?string $user): void {
        $client = null === $user ? static::createBasicClient() : static::createClientWithCredentials(['email' => static::$fixtures[$user]->getEmail()]);
        $client->request('GET', $this->url(), ['headers' => [
            'accept' => 'image/webp',
            'if-none-match' => '"'.$this->image->imageBlob->sha256.'"',
        ]]);
        self::assertResponseStatusCodeSame(null === $user ? 401 : 403);
        self::assertResponseNotHasHeader('X-Image-File');
    }

    public static function unauthorizedUsers(): iterable {
        yield 'anonymous' => [null];

        yield 'unrelated' => ['user4unrelated'];

        yield 'inactive' => ['user5inactive'];
    }

    public function testImageCannotBeReadThroughAnotherCamp(): void {
        static::createClientWithCredentials()->request('GET', '/camps/'.static::$fixtures['camp2']->getId().'/images/'.$this->image->getId(), ['headers' => ['accept' => 'image/webp']]);
        self::assertResponseStatusCodeSame(404);
    }

    public function testSharedCampImageCanBeReadAnonymously(): void {
        $em = static::getContainer()->get(EntityManagerInterface::class);
        $this->image->camp = $em->find(Camp::class, static::$fixtures['campShared']->getId());
        $em->flush();
        static::createBasicClient()->request('GET', $this->url(), ['headers' => ['accept' => 'image/webp']]);
        self::assertResponseIsSuccessful();
    }

    private function url(): string {
        return '/camps/'.$this->image->camp->getId().'/images/'.$this->image->getId();
    }
}
