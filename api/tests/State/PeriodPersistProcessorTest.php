<?php

namespace App\Tests\DataPersister;

use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Day;
use App\Entity\DayResponsible;
use App\Entity\Period;
use App\Entity\ScheduleEntry;
use App\State\PeriodPersistProcessor;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class PeriodPersistProcessorTest extends TestCase {
    private Period $emptyPeriod;
    private Period $period;
    private ScheduleEntry $scheduleEntry;
    private DayResponsible $dayResponsible;

    private MockObject|EntityManagerInterface $em;

    private PeriodPersistProcessor $processor;

    /**
     * @throws \ReflectionException
     */
    protected function setUp(): void {
        $this->emptyPeriod = new Period();
        $this->emptyPeriod->start = new \DateTime('2000-01-10');
        $this->emptyPeriod->end = new \DateTime('2000-01-12');

        $this->period = clone $this->emptyPeriod;
        for ($i = 0; $i < 3; ++$i) {
            $day = new Day();
            $day->dayOffset = $i;
            $this->period->addDay($day);
        }
        $this->scheduleEntry = new ScheduleEntry();
        $this->scheduleEntry->startOffset = 1440 + 600;
        $this->period->addScheduleEntry($this->scheduleEntry);

        $day2 = $this->period->days[1];
        $this->dayResponsible = new DayResponsible();
        $day2->addDayResponsible($this->dayResponsible);

        $decoratedProcessor = $this->createMock(ProcessorInterface::class);
        $this->em = $this->createMock(EntityManagerInterface::class);

        $this->processor = new PeriodPersistProcessor(
            $decoratedProcessor,
            $this->em
        );
    }

    public function testCreateDaysForNewPeriod() {
        $this->processor->onBefore($this->emptyPeriod, new Post());

        // New Period -> create days
        $this->assertCount(3, $this->emptyPeriod->days);
        $this->assertEquals(0, $this->emptyPeriod->days[0]->dayOffset);
        $this->assertEquals(1, $this->emptyPeriod->days[1]->dayOffset);
        $this->assertEquals(2, $this->emptyPeriod->days[2]->dayOffset);
    }

    public function testAddDayAtStartMoveData() {
        // given
        $patchData = clone $this->period;
        $patchData->moveScheduleEntries = true;
        $patchData->start = new \DateTime('2000-01-09');

        // when
        $this->processor->onBefore($patchData, new Patch(), [], ['previous_data' => $this->period]);

        // then
        $this->assertEquals(1440 + 600, $this->scheduleEntry->startOffset);
        $this->assertEquals(1, $this->dayResponsible->day->dayOffset);
    }

    public function testAddDayAtStartNoMoveData() {
        // given
        $patchData = clone $this->period;
        $patchData->moveScheduleEntries = false;
        $patchData->start = new \DateTime('2000-01-09');

        // when
        $this->processor->onBefore($patchData, new Patch(), [], ['previous_data' => $this->period]);

        // then
        $this->assertEquals(1440 + 1440 + 600, $this->scheduleEntry->startOffset);
        $this->assertEquals(2, $this->dayResponsible->day->dayOffset);
    }

    public function testRemoveDayAtStartMoveData() {
        // given
        $patchData = clone $this->period;
        $patchData->moveScheduleEntries = true;
        $patchData->start = new \DateTime('2000-01-11');

        // when
        $this->processor->onBefore($patchData, new Patch(), [], ['previous_data' => $this->period]);

        // then
        $this->assertEquals(1440 + 600, $this->scheduleEntry->startOffset);
        $this->assertEquals(1, $this->dayResponsible->day->dayOffset);
    }

    public function testRemoveDayAtStartNoMoveData() {
        // given
        $patchData = clone $this->period;
        $patchData->moveScheduleEntries = false;
        $patchData->start = new \DateTime('2000-01-11');

        // when
        $this->processor->onBefore($patchData, new Patch(), [], ['previous_data' => $this->period]);

        // then
        $this->assertEquals(600, $this->scheduleEntry->startOffset);
        $this->assertEquals(0, $this->dayResponsible->day->dayOffset);
    }
}
