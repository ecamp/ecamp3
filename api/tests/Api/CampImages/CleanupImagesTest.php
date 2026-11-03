<?php

namespace App\Tests\Api\CampImages;

use App\Entity\Camp;
use App\Entity\CampImage;
use App\Entity\ContentNode\SingleText;
use App\Entity\ImageBlob;
use App\Service\Image\HtmlImageReferences;
use App\Service\Image\ImageCleanupService;
use App\Service\Image\ImageLifecycleLock;
use App\Service\Image\ImageStorage;
use App\Tests\Api\ECampApiTestCase;
use Doctrine\DBAL\Connection;
use Symfony\Component\Clock\MockClock;

/** @internal */
class CleanupImagesTest extends ECampApiTestCase {
    private Connection $db;
    private MockClock $clock;
    private ImageCleanupService $cleanup;
    private CampImage $image;
    private ImageBlob $blob;

    public function setUp(): void {
        parent::setUp();
        $em = $this->getEntityManager();
        $this->db = $em->getConnection();
        $this->clock = new MockClock('2026-11-01 12:00:00 UTC');
        $this->cleanup = $this->createCleanup($this->createStub(ImageStorage::class));
        $this->blob = new ImageBlob();
        $this->blob->sha256 = hash('sha256', 'cleanup test image');
        $this->blob->mediaType = 'image/webp';
        $this->blob->byteSize = 10;
        $this->blob->width = 20;
        $this->blob->height = 10;
        $this->image = new CampImage();
        $this->image->camp = $em->find(Camp::class, static::getFixture('camp1')->getId());
        $this->image->imageBlob = $this->blob;
        $em->persist($this->blob);
        $em->persist($this->image);
        $em->flush();
    }

    public function testMarksOnceAndDeletesOnlyAfterTheConfiguredCampImagePeriod(): void {
        $this->cleanup->cleanupCampImages();
        self::assertSame('2026-11-01 12:00:00', $this->imageTimestamp());
        $this->clock->modify('+3 days');
        $this->cleanup->cleanupCampImages();
        self::assertSame('2026-11-01 12:00:00', $this->imageTimestamp());
        $this->clock->modify('+1 second');
        $this->cleanup->cleanupCampImages();
        self::assertFalse($this->imageTimestamp());
        self::assertNull($this->blobTimestamp());
    }

    public function testReactivatesAnImageReferencedByHtml(): void {
        $this->cleanup->cleanupCampImages();
        $this->clock->modify('+4 days');
        $this->referenceImage();

        $this->cleanup->cleanupCampImages();

        self::assertNull($this->imageTimestamp());
        $this->referenceImage('');
        $this->cleanup->cleanupCampImages();
        self::assertSame('2026-11-05 12:00:00', $this->imageTimestamp());
    }

    public function testBlobRetentionStartsAfterItsLastCampImageIsDeleted(): void {
        $storage = $this->createMock(ImageStorage::class);
        $storage->expects(self::once())->method('delete')->with(self::callback(fn (ImageBlob $blob) => $blob->sha256 === $this->blob->sha256));
        $cleanup = $this->createCleanup($storage);
        $cleanup->cleanupImageBlobs();
        self::assertNull($this->blobTimestamp());
        $cleanup->cleanupCampImages();
        $this->clock->modify('+4 days');
        $cleanup->cleanupCampImages();
        $cleanup->cleanupImageBlobs();
        self::assertSame('2026-11-05 12:00:00', $this->blobTimestamp());
        $this->clock->modify('+5 days');
        $cleanup->cleanupImageBlobs();
        self::assertSame('2026-11-05 12:00:00', $this->blobTimestamp());
        $this->clock->modify('+1 second');
        $cleanup->cleanupImageBlobs();
        self::assertFalse($this->blobTimestamp());
    }

    public function testRetainsSharedBlobsAndClearsTheirTimestamp(): void {
        $this->db->update('image_blob', ['unreferencedsince' => '2020-01-01'], ['id' => $this->blob->getId()]);
        $other = new CampImage();
        $other->camp = $this->getEntityManager()->find(Camp::class, static::getFixture('camp2')->getId());
        $other->imageBlob = $this->blob;
        $this->getEntityManager()->persist($other);
        $this->getEntityManager()->flush();
        $this->db->delete('camp_image', ['id' => $this->image->getId()]);

        $this->cleanup->cleanupImageBlobs();

        self::assertNull($this->blobTimestamp());
    }

    public function testStorageFailureKeepsTheBlobForRetry(): void {
        $this->db->delete('camp_image', ['id' => $this->image->getId()]);
        $this->db->update('image_blob', ['unreferencedsince' => '2020-01-01'], ['id' => $this->blob->getId()]);
        $storage = $this->createMock(ImageStorage::class);
        $storage->expects(self::once())->method('delete')->willThrowException(new \RuntimeException('Storage unavailable'));

        try {
            $this->createCleanup($storage)->cleanupImageBlobs();
            self::fail('Storage failure must be reported.');
        } catch (\RuntimeException $exception) {
            self::assertSame('Storage unavailable', $exception->getMessage());
        }

        self::assertSame('2020-01-01 00:00:00', $this->blobTimestamp());
    }

    public function testSavingAReferenceClearsBothTimestamps(): void {
        $client = static::createClientWithCredentials();
        $this->db->update('camp_image', ['unreferencedsince' => '2020-01-01'], ['id' => $this->image->getId()]);
        $this->db->update('image_blob', ['unreferencedsince' => '2020-01-01'], ['id' => $this->blob->getId()]);
        $this->getEntityManager()->clear();

        $client->request('PATCH', '/content_node/single_texts/'.static::getFixture('singleText1')->getId(), [
            'headers' => ['Content-Type' => 'application/merge-patch+json'],
            'json' => ['data' => ['html' => '<img src="/api/camps/'.$this->image->camp->getId().'/images/'.$this->image->getId().'">']],
        ]);

        self::assertResponseStatusCodeSame(200);
        self::assertNull($this->imageTimestamp());
        self::assertNull($this->blobTimestamp());
    }

    public function testProcessesMoreThanOneBatch(): void {
        $em = $this->getEntityManager();
        for ($i = 0; $i < 101; ++$i) {
            $blob = new ImageBlob();
            $blob->sha256 = hash('sha256', 'batch image '.$i);
            $blob->mediaType = 'image/webp';
            $blob->byteSize = 10;
            $blob->width = 20;
            $blob->height = 10;
            $image = new CampImage();
            $image->camp = $this->image->camp;
            $image->imageBlob = $blob;
            $em->persist($blob);
            $em->persist($image);
        }
        $em->flush();

        $counts = $this->cleanup->cleanupCampImages();
        self::assertSame(102, $counts['marked']);
        $this->clock->modify('+4 days');
        self::assertSame(102, $this->cleanup->cleanupCampImages()['deleted']);
        self::assertSame(102, $this->cleanup->cleanupImageBlobs()['marked']);
        $this->clock->modify('+6 days');
        self::assertSame(102, $this->cleanup->cleanupImageBlobs()['deleted']);
    }

    public function testRejectsNonPositiveRetention(): void {
        $this->expectException(\InvalidArgumentException::class);
        new ImageCleanupService($this->db, new ImageLifecycleLock($this->db), new HtmlImageReferences(), $this->createStub(ImageStorage::class), $this->clock, 0, 5);
    }

    private function createCleanup(ImageStorage $storage): ImageCleanupService {
        return new ImageCleanupService($this->db, new ImageLifecycleLock($this->db), new HtmlImageReferences(), $storage, $this->clock, 3, 5);
    }

    private function referenceImage(?string $html = null): void {
        $note = $this->getEntityManager()->find(SingleText::class, static::getFixture('singleText1')->getId());
        $note->data = ['html' => $html ?? '<p><IMG SRC="/camps/'.$this->image->camp->getId().'/images/'.$this->image->getId().'" alt="Tent"></p>'];
        $this->getEntityManager()->flush();
    }

    private function imageTimestamp(): false|string|null {
        return $this->db->fetchOne('SELECT unreferencedsince FROM camp_image WHERE id = :id', ['id' => $this->image->getId()]);
    }

    private function blobTimestamp(): false|string|null {
        return $this->db->fetchOne('SELECT unreferencedsince FROM image_blob WHERE id = :id', ['id' => $this->blob->getId()]);
    }
}
