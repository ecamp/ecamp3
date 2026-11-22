<?php

namespace App\Tests\Api\CampImages;

use ApiPlatform\Metadata\Post;
use App\Entity\Activity;
use App\Entity\Camp;
use App\Entity\CampImage;
use App\Entity\Category;
use App\Entity\ContentNode\SingleText;
use App\Entity\ContentNode\Storyboard;
use App\Entity\ContentType;
use App\Service\Image\HtmlImageReferences;
use App\Tests\Api\ECampApiTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * @internal
 */
class CopyCampImageTest extends ECampApiTestCase {
    #[DataProvider('copyOperations')]
    public function testCopiesImageReferencesIntoTheTargetCamp(string $entityClass, string $endpoint, string $sourceFixture, string $nodeClass = SingleText::class): void {
        $client = static::createClientWithCredentials();
        $client->disableReboot();
        $em = $this->getEntityManager();
        $source = $em->find(Camp::class, static::getFixture('camp1')->getId());
        $image = new CampImage();
        $image->camp = $source;
        $image->sha256 = hash('sha256', 'copied image');
        $image->mediaType = 'image/avif';
        $em->persist($image);

        $sourceClass = 'activity1' === $sourceFixture ? Activity::class : Category::class;
        $root = $em->find($sourceClass, static::getFixture($sourceFixture)->getId())->getRootContentNode();
        $note = new $nodeClass();
        $note->contentType = $em->find(ContentType::class, static::getFixture(Storyboard::class === $nodeClass ? 'contentTypeStoryboard' : 'contentTypeNotes')->getId());
        $note->instanceName = 'copied image note';
        $note->slot = '1';
        $note->root = $root;
        $html = '<p>Text<img src="/api/images/'.$image->sha256.'/content" alt="Zelt" width="320"></p>';
        $sectionKey = 'cb26d76a-9e3b-43f0-a7c4-0f2ad0ea8029';
        $note->data = Storyboard::class === $nodeClass
            ? ['sections' => [$sectionKey => ['column1' => '', 'column2Html' => $html, 'column3' => '', 'position' => 0]]]
            : ['html' => $html];
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
        $copiedNote = $this->getEntityManager()->getRepository($nodeClass)->findOneBy(['root' => $targetRoot, 'instanceName' => $note->instanceName]);
        $targetImages = $this->getEntityManager()->getRepository(CampImage::class)->findBy(['camp' => $targetCamp]);

        self::assertCount(1, $targetImages);
        $targetImage = $targetImages[0];
        self::assertNotSame($image->getId(), $targetImage->getId());
        self::assertSame($image->sha256, $targetImage->sha256);
        self::assertNotNull($copiedNote);
        $copiedHtml = implode("\n", HtmlImageReferences::htmlFromData($copiedNote->data));
        self::assertStringContainsString('/api/images/'.$targetImage->sha256.'/content', $copiedHtml);
        self::assertStringContainsString('alt="Zelt"', $copiedHtml);
        self::assertStringContainsString('width="320"', $copiedHtml);
        self::assertStringContainsString('/api/images/'.$image->sha256.'/content', implode("\n", HtmlImageReferences::htmlFromData($note->data)));

        $editedData = Storyboard::class === $nodeClass
            ? ['sections' => [$sectionKey => ['column2Html' => $copiedHtml.'<p>Edited</p>']]]
            : ['html' => $copiedHtml.'<p>Edited</p>'];
        $client->request('PATCH', '/content_node/'.(Storyboard::class === $nodeClass ? 'storyboards' : 'single_texts').'/'.$copiedNote->getId(), [
            'headers' => ['Content-Type' => 'application/merge-patch+json'],
            'json' => ['data' => $editedData],
        ]);
        self::assertResponseStatusCodeSame(200);
    }

    public static function copyOperations(): iterable {
        yield 'activity' => [Activity::class, '/activities', 'activity1'];

        yield 'category' => [Category::class, '/categories', 'activity1'];

        yield 'camp' => [Camp::class, '/camps', 'category1'];

        yield 'activity storyboard' => [Activity::class, '/activities', 'activity1', Storyboard::class];

        yield 'category storyboard' => [Category::class, '/categories', 'activity1', Storyboard::class];

        yield 'camp storyboard' => [Camp::class, '/camps', 'category1', Storyboard::class];
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
