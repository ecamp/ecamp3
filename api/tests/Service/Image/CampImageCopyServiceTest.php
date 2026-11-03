<?php

namespace App\Tests\Service\Image;

use App\Entity\Camp;
use App\Entity\CampImage;
use App\Entity\ContentNode\SingleText;
use App\Entity\ImageBlob;
use App\Service\Image\CampImageCopyService;
use App\Util\EntityMap;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

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
        $this->source->imageBlob = new ImageBlob();

        $repository = $this->createStub(EntityRepository::class);
        $repository->method('findOneBy')->willReturnCallback(function (array $criteria): ?CampImage {
            if (isset($criteria['id'])) {
                return $criteria['id'] === $this->source->getId() && $criteria['camp'] === $this->source->camp->getId() ? $this->source : null;
            }

            return $this->existing;
        });
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->em->method('getRepository')->willReturn($repository);
        $security = $this->createStub(Security::class);
        $security->method('isGranted')->willReturnCallback(function (string $attribute, mixed $subject): bool {
            self::assertSame($this->source, $subject);

            return 'CAMP_COLLABORATOR' === $attribute ? $this->collaborator : $this->public;
        });
        $urlGenerator = $this->createStub(UrlGeneratorInterface::class);
        $urlGenerator->method('generate')->willReturnCallback(fn (string $route, array $parameters) => '/api/camps/'.$parameters['campId'].'/images/'.$parameters['id']);
        $this->service = new CampImageCopyService($this->em, $security, $urlGenerator);
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
        self::assertSame($this->source->imageBlob, $target->imageBlob);
        self::assertNotSame($this->source->getId(), $target->getId());
        $url = '/api/camps/'.$target->camp->getId().'/images/'.$target->getId();
        self::assertSame('<p>Vorher <strong><img src="'.$url.'" alt="Zelt &amp; Wald" width="320"></strong> nachher</p>', $first->data['html']);
        self::assertStringContainsString('src="'.$url.'"', $second->data['html']);
    }

    public function testReusesExistingCampImage(): void {
        $this->existing = new CampImage();
        $this->existing->camp = new Camp();
        $this->existing->imageBlob = $this->source->imageBlob;
        $this->existing->unreferencedSince = new \DateTime();
        $this->source->imageBlob->unreferencedSince = new \DateTime();
        $map = new EntityMap($this->existing->camp);
        $node = $this->addNote($map, '<img src="'.$this->sourceUrl().'">');
        $this->em->expects(self::never())->method('persist');

        $this->service->copy($map);

        self::assertStringContainsString('/images/'.$this->existing->getId(), $node->data['html']);
        self::assertNull($this->existing->unreferencedSince);
        self::assertNull($this->source->imageBlob->unreferencedSince);
    }

    public function testKeepsReferencesWithinTheSameCampUnchanged(): void {
        $this->source->unreferencedSince = new \DateTime();
        $this->source->imageBlob->unreferencedSince = new \DateTime();
        $map = new EntityMap($this->source->camp);
        $html = '<p><img src="'.$this->sourceUrl().'" alt="" /></p>';
        $node = $this->addNote($map, $html);
        $this->em->expects(self::never())->method('persist');

        $this->service->copy($map);

        self::assertSame($html, $node->data['html']);
        self::assertNull($this->source->unreferencedSince);
        self::assertNull($this->source->imageBlob->unreferencedSince);
    }

    public function testAllowsReadingPublicImagesWithoutCollaboration(): void {
        $this->collaborator = false;
        $this->public = true;
        $map = new EntityMap(new Camp());
        $node = $this->addNote($map, '<img src="'.$this->sourceUrl().'">');
        $this->em->expects(self::once())->method('persist');

        $this->service->copy($map);

        self::assertStringContainsString('/camps/'.$map->getTargetCamp()->getId().'/images/', $node->data['html']);
    }

    public function testDeniesCopyingAnUnreadableImage(): void {
        $this->collaborator = false;
        $map = new EntityMap(new Camp());
        $this->addNote($map, '<img src="'.$this->sourceUrl().'">');
        $this->em->expects(self::never())->method('persist');
        $this->expectException(AccessDeniedHttpException::class);

        $this->service->copy($map);
    }

    public function testRejectsAnImageReferencedThroughTheWrongCamp(): void {
        $map = new EntityMap(new Camp());
        $this->addNote($map, '<img src="/api/camps/'.$map->getTargetCamp()->getId().'/images/'.$this->source->getId().'">');
        $this->em->expects(self::never())->method('persist');
        $this->expectException(UnprocessableEntityHttpException::class);

        $this->service->copy($map);
    }

    private function sourceUrl(): string {
        return '/api/camps/'.$this->source->camp->getId().'/images/'.$this->source->getId();
    }

    private function addNote(EntityMap $map, string $html): SingleText {
        $source = new SingleText();
        $source->data = ['html' => $html];
        $target = new SingleText();
        $target->copyFromPrototype($source, $map);

        return $target;
    }
}
