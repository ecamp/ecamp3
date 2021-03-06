<?php

namespace App\Tests\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\DataPersister\ActivityDataPersister;
use App\Entity\Activity;
use App\Entity\Camp;
use App\Entity\Category;
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
    private MockObject | ContextAwareDataPersisterInterface $decoratedMock;
    private MockObject | EntityManagerInterface $entityManagerMock;
    private Activity $activity;

    protected function setUp(): void {
        $this->decoratedMock = $this->createMock(ContextAwareDataPersisterInterface::class);
        $this->entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $this->activity = new Activity();
        $this->activity->category = new Category();

        $this->dataPersister = new ActivityDataPersister($this->decoratedMock, $this->entityManagerMock);
    }

    public function testDelegatesSupportCheckToDecorated() {
        $this->decoratedMock
            ->expects($this->exactly(2))
            ->method('supports')
            ->willReturnOnConsecutiveCalls(true, false)
        ;

        $this->assertTrue($this->dataPersister->supports($this->activity, []));
        $this->assertFalse($this->dataPersister->supports($this->activity, []));
    }

    public function testDoesNotSupportNonActivity() {
        $this->decoratedMock
            ->method('supports')
            ->willReturn(true)
        ;

        $this->assertFalse($this->dataPersister->supports([], []));
    }

    public function testDelegatesPersistToDecorated() {
        // given
        $this->decoratedMock->expects($this->once())
            ->method('persist')
        ;

        // when
        $this->dataPersister->persist($this->activity, []);

        // then
    }

    public function testSetsCampFromCategory() {
        // given
        $camp = $this->createMock(Camp::class);
        $this->activity->category = new Category();
        $this->activity->category->camp = $camp;
        $this->decoratedMock->expects($this->once())->method('persist')->willReturnArgument(0);

        // when
        /** @var Activity $data */
        $data = $this->dataPersister->persist($this->activity, []);

        // then
        $this->assertEquals($camp, $data->getCamp());
    }

    public function testPostCreatesANewRootContentNode() {
        // given
        $this->decoratedMock->expects($this->once())->method('persist')->willReturnArgument(0);
        $repositoryMock = $this->createMock(EntityRepository::class);
        $repositoryMock->method('findOneBy')->willReturnCallback(function ($criteria) {
            $result = new ContentType();
            $result->name = $criteria['name'];

            return $result;
        });
        $this->entityManagerMock->method('getRepository')->willReturn($repositoryMock);

        // when
        /** @var Activity $data */
        $data = $this->dataPersister->persist($this->activity, ['collection_operation_name' => 'post']);

        // then
        $this->assertNotNull($data->getRootContentNode());
        $this->assertEquals('ColumnLayout', $data->getRootContentNode()->contentType->name);
    }

    public function testUpdateDoesNotChangeRootContentNode() {
        // given
        $this->decoratedMock->expects($this->once())->method('persist')->willReturnArgument(0);

        // when
        /** @var Activity $data */
        $data = $this->dataPersister->persist($this->activity, ['item_operation_name' => 'patch']);

        // then
        $this->assertNull($data->getRootContentNode());
    }
}
