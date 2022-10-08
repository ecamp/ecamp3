<?php

namespace App\Tests\DataPersister;

use App\DataPersister\PeriodDataPersister;
use App\DataPersister\Util\DataPersisterObservable;
use App\Entity\Day;
use App\Entity\DayResponsible;
use App\Entity\Period;
use App\Entity\ScheduleEntry;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\UnitOfWork;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class PeriodDataPersisterTest extends TestCase {
    private array $origPeriodData;
    private Period $emptyPeriod;
    private Period $period;
    private ScheduleEntry $scheduleEntry;
    private DayResponsible $dayResponsible;

    private MockObject|DataPersisterObservable $dataPersisterObservable;
    private MockObject|EntityManagerInterface $em;
    private MockObject|UnitOfWork $uow;

    private PeriodDataPersister $dataPersister;

    /**
     * @throws \ReflectionException
     */
    protected function setUp(): void {
        $this->origPeriodData = [
            'start' => new DateTime('2000-01-10'),
            'end' => new DateTime('2000-01-12'),
        ];
        $this->emptyPeriod = new Period();
        $this->emptyPeriod->start = $this->origPeriodData['start'];
        $this->emptyPeriod->end = $this->origPeriodData['end'];

        $this->period = new Period();
        $this->period->start = $this->origPeriodData['start'];
        $this->period->end = $this->origPeriodData['end'];
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

        $this->dataPersisterObservable = $this->createMock(DataPersisterObservable::class);
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->uow = $this->createMock(UnitOfWork::class);

        $this->em->method('getUnitOfWork')->willReturn($this->uow);
        $this->uow->method('getOriginalEntityData')->will($this->returnCallback(
            fn () => $this->origPeriodData
        ));

        $this->dataPersister = new PeriodDataPersister(
            $this->dataPersisterObservable,
            $this->em
        );
    }

    public function testCreateDaysForNewPeriod() {
        $this->dataPersister->beforeCreate($this->emptyPeriod);

        // New Period -> create days
        $this->assertCount(3, $this->emptyPeriod->days);
        $this->assertEquals(0, $this->emptyPeriod->days[0]->dayOffset);
        $this->assertEquals(1, $this->emptyPeriod->days[1]->dayOffset);
        $this->assertEquals(2, $this->emptyPeriod->days[2]->dayOffset);
    }

    public function testAddDayAtStartMoveData() {
        // given
        $this->period->moveScheduleEntries = true;
        $this->period->start = new DateTime('2000-01-09');

        // when
        $this->dataPersister->beforeUpdate($this->period);

        // then
        $this->assertEquals(1440 + 600, $this->scheduleEntry->startOffset);
        $this->assertEquals(1, $this->dayResponsible->day->dayOffset);
    }

    public function testAddDayAtStartNoMoveData() {
        // given
        $this->period->moveScheduleEntries = false;
        $this->period->start = new DateTime('2000-01-09');

        // when
        $this->dataPersister->beforeUpdate($this->period);

        // then
        $this->assertEquals(1440 + 1440 + 600, $this->scheduleEntry->startOffset);
        $this->assertEquals(2, $this->dayResponsible->day->dayOffset);
    }

    public function testRemoveDayAtStartMoveData() {
        // given
        $this->period->moveScheduleEntries = true;
        $this->period->start = new DateTime('2000-01-11');

        // when
        $this->dataPersister->beforeUpdate($this->period);

        // then
        $this->assertEquals(1440 + 600, $this->scheduleEntry->startOffset);
        $this->assertEquals(1, $this->dayResponsible->day->dayOffset);
    }

    public function testRemoveDayAtStartNoMoveData() {
        // given
        $this->period->moveScheduleEntries = false;
        $this->period->start = new DateTime('2000-01-11');

        // when
        $this->dataPersister->beforeUpdate($this->period);

        // then
        $this->assertEquals(600, $this->scheduleEntry->startOffset);
        $this->assertEquals(0, $this->dayResponsible->day->dayOffset);
    }
}
