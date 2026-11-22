<?php

namespace App\Tests\Api\CampImages;

use App\Entity\Camp;
use App\Entity\CampImage;
use App\Entity\ContentNode\SingleText;
use App\Entity\ContentNode\Storyboard;
use App\Service\Image\HtmlImageReferences;
use App\Service\Image\ImageCleanupService;
use App\Tests\Api\ECampApiTestCase;
use Doctrine\DBAL\Connection;
use League\Flysystem\FilesystemOperator;
use PHPUnit\Framework\Attributes\TestWith;
use Symfony\Component\Clock\MockClock;

/** @internal */
class CleanupImagesTest extends ECampApiTestCase {
    private Connection $db;
    private MockClock $clock;
    private ImageCleanupService $cleanup;
    private CampImage $image;

    public function setUp(): void {
        parent::setUp();
        $em = $this->getEntityManager();
        $this->db = $em->getConnection();
        $this->clock = new MockClock('2026-11-01 12:00:00 UTC');
        $this->cleanup = $this->createCleanup($this->createStub(FilesystemOperator::class));
        $this->image = new CampImage();
        $this->image->camp = $em->find(Camp::class, static::getFixture('camp1')->getId());
        $this->image->sha256 = hash('sha256', 'cleanup test image');
        $this->image->mediaType = 'image/avif';
        $em->persist($this->image);
        $em->flush();
    }

    public function testMarksOnceAndKeepsExpiredCampImages(): void {
        $this->cleanup->cleanupCampImages();
        self::assertSame('2026-11-01 12:00:00', $this->imageTimestamp());
        $this->clock->modify('+7 days');
        $this->cleanup->cleanupCampImages();
        self::assertSame('2026-11-01 12:00:00', $this->imageTimestamp());
        $this->clock->modify('+1 second');
        self::assertSame(['marked' => 0, 'reactivated' => 0], $this->cleanup->cleanupCampImages());
        self::assertSame('2026-11-01 12:00:00', $this->imageTimestamp());
    }

    #[TestWith([false])]
    #[TestWith([true])]
    public function testReactivatesAnImageReferencedByHtml(bool $storyboard): void {
        $this->cleanup->cleanupCampImages();
        $this->clock->modify('+4 days');
        $this->referenceImage(storyboard: $storyboard);

        $this->cleanup->cleanupCampImages();

        self::assertNull($this->imageTimestamp());
        $this->referenceImage('', $storyboard);
        $this->cleanup->cleanupCampImages();
        self::assertSame('2026-11-05 12:00:00', $this->imageTimestamp());
    }

    public function testKeepsAFileReferencedOnlyByAStoryboard(): void {
        $this->referenceImage(storyboard: true);

        self::assertSame(['marked' => 0, 'reactivated' => 0], $this->cleanup->cleanupCampImages());
        $this->clock->modify('+8 days');
        $this->cleanup->cleanupCampImages();
        self::assertSame(['images' => 0, 'files' => 0], $this->cleanup->cleanupImageFiles());
        self::assertNull($this->imageTimestamp());
    }

    public function testDeletesEachCampImageAfterItsOwnGracePeriod(): void {
        $other = $this->addImageInAnotherCamp();
        $storage = $this->createMock(FilesystemOperator::class);
        $storage->expects(self::once())->method('delete')->with('images/'.$this->image->sha256);
        $cleanup = $this->createCleanup($storage);

        self::assertSame(['images' => 0, 'files' => 0], $cleanup->cleanupImageFiles());
        $this->markUnused($this->image, '2026-10-20 12:00:00');
        $this->markUnused($other, '2026-11-01 12:00:00');
        self::assertSame(['images' => 1, 'files' => 0], $cleanup->cleanupImageFiles());
        self::assertFalse($this->imageTimestamp());
        self::assertSame('2026-11-01 12:00:00', $this->imageTimestamp($other));

        $this->clock->modify('+7 days');
        self::assertSame(['images' => 0, 'files' => 0], $cleanup->cleanupImageFiles());
        self::assertFalse($this->imageTimestamp());
        self::assertSame('2026-11-01 12:00:00', $this->imageTimestamp($other));

        $this->clock->modify('+1 second');
        self::assertSame(['images' => 1, 'files' => 1], $cleanup->cleanupImageFiles());
        self::assertFalse($this->imageTimestamp());
        self::assertFalse($this->imageTimestamp($other));
    }

    public function testDeletesExpiredCampImageAndRetainsTheFileWhenAnotherCampImageIsUnmarked(): void {
        $storage = $this->createMock(FilesystemOperator::class);
        $storage->expects(self::never())->method('delete');
        $other = $this->addImageInAnotherCamp();
        $this->markUnused($this->image, '2020-01-01');

        self::assertSame(['images' => 1, 'files' => 0], $this->createCleanup($storage)->cleanupImageFiles());
        self::assertFalse($this->imageTimestamp());
        self::assertNull($this->imageTimestamp($other));
    }

    public function testStorageFailureKeepsTheLastCampImageForRetry(): void {
        $other = $this->addImageInAnotherCamp();
        $this->markUnused($this->image, '2020-01-01');
        $this->markUnused($other, '2020-01-02');
        $storage = $this->createMock(FilesystemOperator::class);
        $storage->expects(self::once())->method('delete')->willThrowException(new \RuntimeException('Storage unavailable'));

        try {
            $this->createCleanup($storage)->cleanupImageFiles();
            self::fail('Storage failure must be reported.');
        } catch (\RuntimeException $exception) {
            self::assertSame('Storage unavailable', $exception->getMessage());
        }

        $first = $this->image->getId() < $other->getId() ? $this->image : $other;
        $last = $first === $this->image ? $other : $this->image;
        self::assertFalse($this->imageTimestamp($first));
        self::assertSame($last === $this->image ? '2020-01-01 00:00:00' : '2020-01-02 00:00:00', $this->imageTimestamp($last));

        $retryStorage = $this->createMock(FilesystemOperator::class);
        $retryStorage->expects(self::once())->method('delete')->with('images/'.$this->image->sha256);
        self::assertSame(['images' => 1, 'files' => 1], $this->createCleanup($retryStorage)->cleanupImageFiles());
        self::assertFalse($this->imageTimestamp());
        self::assertFalse($this->imageTimestamp($other));
    }

    public function testDeletingACampKeepsImagesInOtherCamps(): void {
        $other = $this->addImageInAnotherCamp();

        static::createClientWithCredentials()->request('DELETE', '/camps/'.$this->image->camp->getId());

        self::assertResponseStatusCodeSame(204);
        self::assertFalse($this->imageTimestamp());
        self::assertNull($this->imageTimestamp($other));
    }

    public function testSavingAReferenceClearsTheCampImageTimestamp(): void {
        $client = static::createClientWithCredentials();
        $this->markUnused($this->image, '2020-01-01');
        $this->getEntityManager()->clear();

        $client->request('PATCH', '/content_node/single_texts/'.static::getFixture('singleText1')->getId(), [
            'headers' => ['Content-Type' => 'application/merge-patch+json'],
            'json' => ['data' => ['html' => '<img src="/api/images/'.$this->image->sha256.'/content">']],
        ]);

        self::assertResponseStatusCodeSame(200);
        self::assertNull($this->imageTimestamp());
        self::assertSame(['images' => 0, 'files' => 0], $this->cleanup->cleanupImageFiles());
    }

    public function testProcessesMoreThanOneBatch(): void {
        $em = $this->getEntityManager();
        for ($i = 0; $i < 101; ++$i) {
            $image = new CampImage();
            $image->camp = $this->image->camp;
            $image->sha256 = hash('sha256', 'batch image '.$i);
            $image->mediaType = 'image/avif';
            $em->persist($image);
        }
        $em->flush();

        $counts = $this->cleanup->cleanupCampImages();
        self::assertSame(102, $counts['marked']);
        $this->clock->modify('+7 days');
        self::assertSame(['images' => 0, 'files' => 0], $this->cleanup->cleanupImageFiles());
        self::assertSame(['marked' => 0, 'reactivated' => 0], $this->cleanup->cleanupCampImages());
        $this->clock->modify('+1 second');
        self::assertSame(['images' => 102, 'files' => 102], $this->cleanup->cleanupImageFiles());
    }

    private function addImageInAnotherCamp(): CampImage {
        $em = $this->getEntityManager();
        $other = new CampImage();
        $other->camp = $em->find(Camp::class, static::getFixture('camp2')->getId());
        $other->sha256 = $this->image->sha256;
        $other->mediaType = $this->image->mediaType;
        $em->persist($other);
        $em->flush();

        return $other;
    }

    private function markUnused(CampImage $image, string $since): void {
        $this->db->update('camp_image', ['unreferencedsince' => $since], ['id' => $image->getId()]);
    }

    private function createCleanup(FilesystemOperator $storage): ImageCleanupService {
        return new ImageCleanupService($this->db, new HtmlImageReferences(), $storage, $this->clock);
    }

    private function referenceImage(?string $html = null, bool $storyboard = false): void {
        $note = $this->getEntityManager()->find($storyboard ? Storyboard::class : SingleText::class, static::getFixture($storyboard ? 'storyboard1' : 'singleText1')->getId());
        $html ??= '<p><IMG SRC="/images/'.$this->image->sha256.'/content" alt="Tent"></p>';
        if ($storyboard) {
            $note->data['sections']['cb26d76a-9e3b-43f0-a7c4-0f2ad0ea8029']['column2Html'] = $html;
        } else {
            $note->data = ['html' => $html];
        }
        $this->getEntityManager()->flush();
    }

    private function imageTimestamp(?CampImage $image = null): false|string|null {
        return $this->db->fetchOne('SELECT unreferencedsince FROM camp_image WHERE id = :id', ['id' => ($image ?? $this->image)->getId()]);
    }
}
