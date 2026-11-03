<?php

namespace App\Tests\Api\CampImages;

use ApiPlatform\Metadata\Post;
use App\Entity\Activity;
use App\Entity\Camp;
use App\Entity\CampImage;
use App\Entity\Category;
use App\Entity\ContentNode\SingleText;
use App\Entity\ContentType;
use App\Entity\ImageBlob;
use App\Tests\Api\ECampApiTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * @internal
 */
class CopyCampImageTest extends ECampApiTestCase {
    #[DataProvider('copyOperations')]
    public function testCopiesImageReferencesIntoTheTargetCamp(string $entityClass, string $endpoint, string $sourceFixture): void {
        $client = static::createClientWithCredentials();
        $em = $this->getEntityManager();
        $source = $em->find(Camp::class, static::getFixture('camp1')->getId());
        $blob = new ImageBlob();
        $blob->sha256 = hash('sha256', 'copied image');
        $blob->mediaType = 'image/webp';
        $blob->byteSize = 10;
        $blob->width = 640;
        $blob->height = 360;
        $image = new CampImage();
        $image->camp = $source;
        $image->imageBlob = $blob;
        $em->persist($blob);
        $em->persist($image);

        $sourceClass = 'activity1' === $sourceFixture ? Activity::class : Category::class;
        $root = $em->find($sourceClass, static::getFixture($sourceFixture)->getId())->getRootContentNode();
        $note = new SingleText();
        $note->contentType = $em->find(ContentType::class, static::getFixture('contentTypeNotes')->getId());
        $note->instanceName = 'copied image note';
        $note->slot = '1';
        $note->root = $root;
        $note->data = ['html' => '<p>Text<img src="/api/camps/'.$source->getId().'/images/'.$image->getId().'" alt="Zelt" width="320"></p>'];
        $root->addChild($note);
        $root->addRootDescendant($note);
        $em->persist($note);
        $em->flush();

        $response = $client->request('POST', $endpoint, ['json' => $this->copyPayload($entityClass)]);
        self::assertResponseStatusCodeSame(201);
        $created = $this->getEntityManager()->find($entityClass, $response->toArray()['id']);
        $targetCamp = $created instanceof Camp ? $created : $created->getCamp();
        $targetRoot = $created instanceof Camp
            ? $this->getEntityManager()->getRepository(Category::class)->findOneBy(['camp' => $created, 'categoryPrototypeId' => static::getFixture($sourceFixture)->getId()])->getRootContentNode()
            : $created->getRootContentNode();
        $copiedNote = $this->getEntityManager()->getRepository(SingleText::class)->findOneBy(['root' => $targetRoot, 'instanceName' => $note->instanceName]);
        $targetImages = $this->getEntityManager()->getRepository(CampImage::class)->findBy(['camp' => $targetCamp]);

        self::assertCount(1, $targetImages);
        $targetImage = $targetImages[0];
        self::assertNotSame($image->getId(), $targetImage->getId());
        self::assertSame($blob->getId(), $targetImage->imageBlob->getId());
        self::assertNotNull($copiedNote);
        self::assertStringContainsString('/camps/'.$targetCamp->getId().'/images/'.$targetImage->getId(), $copiedNote->data['html']);
        self::assertStringContainsString('alt="Zelt"', $copiedNote->data['html']);
        self::assertStringContainsString('width="320"', $copiedNote->data['html']);
        self::assertStringContainsString('/camps/'.$source->getId().'/images/'.$image->getId(), $note->data['html']);

        $client->request('PATCH', '/content_node/single_texts/'.$copiedNote->getId(), [
            'headers' => ['Content-Type' => 'application/merge-patch+json'],
            'json' => ['data' => ['html' => $copiedNote->data['html'].'<p>Edited</p>']],
        ]);
        self::assertResponseStatusCodeSame(200);
    }

    public static function copyOperations(): iterable {
        yield 'activity' => [Activity::class, '/activities', 'activity1'];

        yield 'category' => [Category::class, '/categories', 'activity1'];

        yield 'camp' => [Camp::class, '/camps', 'category1'];
    }

    private function copyPayload(string $entityClass): array {
        if (Camp::class === $entityClass) {
            return $this->getExamplePayload(Camp::class, Post::class, [
                'campPrototype' => $this->getIriFor('camp1'),
            ], ['hitobitoProvider', 'hitobitoEventId']);
        }
        if (Category::class === $entityClass) {
            return $this->getExamplePayload(Category::class, Post::class, [
                'camp' => $this->getIriFor('camp2'),
                'copyCategorySource' => $this->getIriFor('activity1'),
                'preferredContentTypes' => [],
            ]);
        }

        return $this->getExamplePayload(Activity::class, Post::class, [
            'copyActivitySource' => $this->getIriFor('activity1'),
            'category' => $this->getIriFor('category1camp2'),
            'progressLabel' => null,
            'scheduleEntries' => [
                [
                    'period' => $this->getIriFor('period1camp2'),
                    'start' => '2023-03-25T15:00:00+00:00',
                    'end' => '2023-03-25T16:00:00+00:00',
                ],
            ],
        ]);
    }
}
