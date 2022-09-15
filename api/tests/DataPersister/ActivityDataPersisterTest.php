<?php

namespace App\Tests\DataPersister;

use App\DataPersister\ActivityDataPersister;
use App\DataPersister\Util\DataPersisterObservable;
use App\Entity\Activity;
use App\Entity\Camp;
use App\Entity\Category;
use App\Entity\ContentNode\ColumnLayout;
use App\Entity\ContentType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class ActivityDataPersisterTest extends TestCase {
    private ActivityDataPersister $dataPersister;
    private Activity $activity;
    private MockObject|EntityManagerInterface $em;

    protected function setUp(): void {
        $dataPersisterObservable = $this->createMock(DataPersisterObservable::class);
        $this->em = $this->createMock(EntityManagerInterface::class);

        $this->activity = new Activity();
        $this->activity->category = new Category();

        $camp = $this->createMock(Camp::class);
        $this->activity->category->camp = $camp;

        $contentType = new ContentType();
        $contentType->name = 'ColumnLayout';

        $categoryRoot = new ColumnLayout();
        $categoryRoot->instanceName = 'category root';
        $categoryRoot->contentType = $contentType;
        $this->activity->category->setRootContentNode($categoryRoot);

        $repository = $this->createMock(EntityRepository::class);
        $this->em->method('getRepository')->willReturn($repository);
        $repository->method('findOneBy')->willReturn($contentType);

        $this->dataPersister = new ActivityDataPersister($dataPersisterObservable, $this->em);
    }

    public function testSetsCampFromCategory() {
        // when
        /** @var Activity $data */
        $data = $this->dataPersister->beforeCreate($this->activity);

        // then
        $this->assertEquals($this->activity->category->camp, $data->getCamp());
    }

    public function testPostCopiesContentFromCategory() {
        // when
        /** @var Activity $data */
        $data = $this->dataPersister->beforeCreate($this->activity);

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
        /** @var Activity $data */
        $data = $this->dataPersister->beforeCreate($this->activity);
    }
}
