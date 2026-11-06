<?php

namespace App\Tests\State;

use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Activity;
use App\Entity\Camp;
use App\Entity\Category;
use App\Entity\ContentNode\ColumnLayout;
use App\Entity\ContentType;
use App\State\ActivityCreateProcessor;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use FOS\HttpCacheBundle\CacheManager;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class ActivityCreateProcessorTest extends TestCase {
    private ActivityCreateProcessor $processor;
    private Activity $activity;

    protected function setUp(): void {
        $decoratedProcessor = $this->createStub(ProcessorInterface::class);
        $em = $this->createStub(EntityManagerInterface::class);

        $this->activity = new Activity();
        $this->activity->category = new Category();

        $camp = $this->createStub(Camp::class);
        $this->activity->category->camp = $camp;

        $contentType = new ContentType();
        $contentType->name = 'ColumnLayout';

        $categoryRoot = new ColumnLayout();
        $categoryRoot->instanceName = 'category root';
        $categoryRoot->contentType = $contentType;
        $this->activity->category->setRootContentNode($categoryRoot);

        // EntityManager
        $repository = $this->createStub(EntityRepository::class);
        $em->method('getRepository')->willReturn($repository);
        $repository->method('findOneBy')->willReturn($contentType);

        $this->processor = new ActivityCreateProcessor($decoratedProcessor, $em, $this->createStub(CacheManager::class));
    }

    public function testSetsCampFromCategory() {
        // when
        $data = $this->processor->onBefore($this->activity, new Post());

        // then
        $this->assertEquals($this->activity->category->camp, $data->getCamp());
    }

    public function testPostCopiesContentFromCategory() {
        // when
        $data = $this->processor->onBefore($this->activity, new Post());

        // then
        $this->assertNotNull($data->getRootContentNode());
        $this->assertNotEquals($this->activity->category->getRootContentNode(), $data->getRootContentNode());
        $this->assertEquals('category root', $data->getRootContentNode()->instanceName);
        $this->assertEquals('ColumnLayout', $data->getRootContentNode()->contentType->name);
    }

    public function testPostFailsForMissingCategoryRootContentNode() {
        // given
        $this->activity->category->setRootContentNode(null);

        // then
        $this->expectException(\UnexpectedValueException::class);

        // when
        // @var Activity $data
        $this->processor->onBefore($this->activity, new Post());
    }
}
