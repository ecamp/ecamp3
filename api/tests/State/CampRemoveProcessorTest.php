<?php

namespace App\Tests\State;

use ApiPlatform\Metadata\Delete;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Activity;
use App\Entity\Camp;
use App\Entity\Category;
use App\Entity\ContentNode\ColumnLayout;
use App\State\CampRemoveProcessor;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class CampRemoveProcessorTest extends TestCase {
    private CampRemoveProcessor $processor;
    private MockObject|EntityManagerInterface $em;
    private Camp $camp;

    protected function setUp(): void {
        $this->camp = new Camp();

        $this->em = $this->createMock(EntityManagerInterface::class);
        $decoratedProcessor = $this->createMock(ProcessorInterface::class);
        $this->processor = new CampRemoveProcessor($decoratedProcessor, $this->em);
    }

    public function testRemovesRootContentNodes() {
        // given
        $activity = new Activity();
        $activity->setRootContentNode(new ColumnLayout());
        $this->camp->addActivity($activity);

        $category = new Category();
        $category->setRootContentNode(new ColumnLayout());
        $this->camp->addCategory($category);

        // then
        $this->em
            ->expects($this->exactly(2))
            ->method('remove')
            ->withConsecutive(
                [
                    $activity->getRootContentNode(),
                ],
                [
                    $category->getRootContentNode(),
                ]
            )
        ;

        // when
        $this->processor->onBefore($this->camp, new Delete());
    }
}
