<?php

namespace App\Tests\State;

use ApiPlatform\Metadata\Delete;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Activity;
use App\Entity\ContentNode\ColumnLayout;
use App\State\ActivityRemoveProcessor;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class ActivityRemoveProcessorTest extends TestCase {
    private ActivityRemoveProcessor $processor;
    private Activity $activity;
    private MockObject|EntityManagerInterface $em;

    protected function setUp(): void {
        $decoratedProcessor = $this->createMock(ProcessorInterface::class);
        $this->em = $this->createMock(EntityManagerInterface::class);

        $this->activity = new Activity();

        $root = new ColumnLayout();
        $this->activity->setRootContentNode($root);

        $this->processor = new ActivityRemoveProcessor($decoratedProcessor, $this->em);
    }

    public function testRemovesRootContentNode() {
        // then
        $this->em->expects($this->exactly(1))->method('remove')->with($this->activity->getRootContentNode());

        // when
        $this->processor->onBefore($this->activity, new Delete());
    }
}
