<?php

namespace App\Tests\Service\Image;

use App\Entity\Camp;
use App\Entity\CampImage;
use App\Entity\ContentNode\SingleText;
use App\Service\Image\CampImageCopyService;
use App\Service\Image\CampImageReadAccess;
use App\Util\EntityMap;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @internal
 */
class CampImageCopyServiceTest extends TestCase {
    private EntityManagerInterface&MockObject $em;
    private CampImageCopyService $service;
    private CampImage $source;
    private ?CampImage $existing = null;
    private bool $collaborator = true;
    private bool $public = false;

    protected function setUp(): void {
        $this->source = new CampImage();
        $this->source->camp = new Camp();
        $this->source->sha256 = hash('sha256', 'source image');
        $this->source->mediaType = 'image/avif';

        $repository = $this->createStub(EntityRepository::class);
        $repository->method('findBy')->willReturnCallback(fn (array $criteria) => $criteria['sha256'] === $this->source->sha256 ? [$this->source] : []);
        $repository->method('findOneBy')->willReturnCallback(fn () => $this->existing);
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->em->method('getRepository')->willReturn($repository);
        $security = $this->createStub(Security::class);
        $security->method('isGranted')->willReturnCallback(function (string $attribute, mixed $subject): bool {
            self::assertSame($this->source, $subject);

            return 'CAMP_COLLABORATOR' === $attribute ? $this->collaborator : $this->public;
        });
        $this->service = new CampImageCopyService($this->em, new CampImageReadAccess($this->em, $security));
    }

    public function testCopiesImageOnceAcrossMultipleNotesAndPreservesHtmlAttributes(): void {
        $map = new EntityMap(new Camp());
        $first = $this->addNote($map, '<p>Vorher <strong><img src="'.$this->sourceUrl().'" alt="Zelt &amp; Wald" width="320"></strong> nachher</p>');
        $second = $this->addNote($map, '<p><img src="'.substr($this->sourceUrl(), 4).'"></p>');
        $target = null;
        $this->em->expects(self::once())->method('persist')->willReturnCallback(function (CampImage $image) use (&$target): void {
            $target = $image;
        });

        $this->service->copy($map);

        self::assertInstanceOf(CampImage::class, $target);
        self::assertSame($map->getTargetCamp(), $target->camp);
        self::assertSame($this->source->sha256, $target->sha256);
        self::assertSame($this->source->mediaType, $target->mediaType);
        self::assertNotSame($this->source->getId(), $target->getId());
        $url = $this->sourceUrl();
        self::assertSame('<p>Vorher <strong><img src="'.$url.'" alt="Zelt &amp; Wald" width="320"></strong> nachher</p>', $first->data['html']);
        self::assertStringContainsString('src="'.substr($url, 4).'"', $second->data['html']);
    }

    public function testReusesExistingCampImage(): void {
        $this->existing = new CampImage();
        $this->existing->camp = new Camp();
        $this->existing->sha256 = $this->source->sha256;
        $this->existing->mediaType = $this->source->mediaType;
        $this->existing->unreferencedSince = new \DateTime();
        $map = new EntityMap($this->existing->camp);
        $node = $this->addNote($map, '<img src="'.$this->sourceUrl().'">');
        $this->em->expects(self::never())->method('persist');

        $this->service->copy($map);

        self::assertStringContainsString($this->sourceUrl(), $node->data['html']);
        self::assertNull($this->existing->unreferencedSince);
        self::assertSame($this->source->sha256, $this->existing->sha256);
    }

    public function testKeepsReferencesWithinTheSameCampUnchanged(): void {
        $this->source->unreferencedSince = new \DateTime();
        $map = new EntityMap($this->source->camp);
        $html = '<p><img src="'.$this->sourceUrl().'" alt="" /></p>';
        $node = $this->addNote($map, $html);
        $this->em->expects(self::never())->method('persist');

        $this->service->copy($map);

        self::assertSame($html, $node->data['html']);
        self::assertNull($this->source->unreferencedSince);
        self::assertStringContainsString($this->sourceUrl(), $node->data['html']);
    }

    public function testAllowsReadingPublicImagesWithoutCollaboration(): void {
        $this->collaborator = false;
        $this->public = true;
        $map = new EntityMap(new Camp());
        $node = $this->addNote($map, '<img src="'.$this->sourceUrl().'">');
        $this->em->expects(self::once())->method('persist');

        $this->service->copy($map);

        self::assertStringContainsString($this->sourceUrl(), $node->data['html']);
    }

    public function testDeniesCopyingAnUnreadableImage(): void {
        $this->collaborator = false;
        $map = new EntityMap(new Camp());
        $this->addNote($map, '<img src="'.$this->sourceUrl().'">');
        $this->em->expects(self::never())->method('persist');
        $this->expectException(AccessDeniedException::class);

        $this->service->copy($map);
    }

    public function testRejectsAnUnknownImageHash(): void {
        $map = new EntityMap(new Camp());
        $this->addNote($map, '<img src="/api/images/'.str_repeat('0', 64).'/content">');
        $this->em->expects(self::never())->method('persist');
        $this->expectException(UnprocessableEntityHttpException::class);

        $this->service->copy($map);
    }

    private function sourceUrl(): string {
        return '/api/images/'.$this->source->sha256.'/content';
    }

    private function addNote(EntityMap $map, string $html): SingleText {
        $source = new SingleText();
        $source->data = ['html' => $html];
        $target = new SingleText();
        $target->copyFromPrototype($source, $map);

        return $target;
    }
}
