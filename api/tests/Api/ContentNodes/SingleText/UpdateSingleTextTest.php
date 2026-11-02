<?php

namespace App\Tests\Api\ContentNodes\SingleText;

use App\Entity\Camp;
use App\Entity\CampImage;
use App\Entity\ImageBlob;
use App\Tests\Api\ContentNodes\UpdateContentNodeTestCase;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @internal
 */
class UpdateSingleTextTest extends UpdateContentNodeTestCase {
    #[\Override]
    public function setUp(): void {
        parent::setUp();

        $this->endpoint = '/content_node/single_texts';
        $this->defaultEntity = static::getFixture('singleText1');
        $this->campPrototypeEntity = static::getFixture('singleTextCampPrototype');
        $this->sharedCampEntity = static::getFixture('singleTextCampShared');
    }

    public function testPatchPreservesCampImageDescriptionAndWidth(): void {
        $client = static::createClientWithCredentials();
        $em = static::getContainer()->get(EntityManagerInterface::class);
        $blob = new ImageBlob();
        $blob->sha256 = hash('sha256', 'single-text-image');
        $blob->mediaType = 'image/webp';
        $blob->byteSize = 10;
        $blob->width = 640;
        $blob->height = 360;
        $image = new CampImage();
        $image->camp = $em->find(Camp::class, static::$fixtures['camp1']->getId());
        $image->imageBlob = $blob;
        $em->persist($blob);
        $em->persist($image);
        $em->flush();
        $src = '/api/camps/'.$image->camp->getId().'/images/'.$image->getId();
        $response = $client->request('PATCH', $this->endpoint.'/'.$this->defaultEntity->getId(), [
            'headers' => ['Content-Type' => 'application/merge-patch+json'],
            'json' => ['data' => ['html' => '<p><img src="'.$src.'" alt="Tent" width="320"></p>']],
        ]);
        self::assertResponseStatusCodeSame(200);
        $html = $response->toArray()['data']['html'];
        self::assertStringContainsString('src="'.$src.'"', $html);
        self::assertStringContainsString('alt="Tent"', $html);
        self::assertStringContainsString('width="320"', $html);
    }

    public function testPatchRejectsAnUnknownCampImage(): void {
        $client = static::createClientWithCredentials();
        $src = '/api/camps/'.static::$fixtures['camp1']->getId().'/images/000000000000';
        $client->request('PATCH', $this->endpoint.'/'.$this->defaultEntity->getId(), [
            'headers' => ['Content-Type' => 'application/merge-patch+json'],
            'json' => ['data' => ['html' => '<img src="'.$src.'" alt="">']],
        ]);
        self::assertResponseStatusCodeSame(422);
    }

    public function testPatchRejectsAnExternalImage(): void {
        static::createClientWithCredentials()->request('PATCH', $this->endpoint.'/'.$this->defaultEntity->getId(), [
            'headers' => ['Content-Type' => 'application/merge-patch+json'],
            'json' => ['data' => ['html' => '<img src="https://example.org/image.png" alt="">']],
        ]);
        self::assertResponseStatusCodeSame(422);
    }

    public function testPatchText() {
        // when
        $this->patch($this->defaultEntity, ['data' => ['html' => 'testText']]);

        // then
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains(['data' => [
            'html' => 'testText',
        ]]);
    }

    public function testPatchCleansHTMLFromText() {
        // when
        $this->patch($this->defaultEntity, ['data' => ['html' => ' testText<script>alert(1)</script>']]);

        // then
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains(['data' => [
            'html' => ' testText',
        ]]);
    }
}
