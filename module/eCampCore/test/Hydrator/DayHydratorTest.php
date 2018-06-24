<?php

namespace eCamp\CoreTest\Hydrator;

use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\Day;
use eCamp\Core\Entity\Period;
use eCamp\Core\Hydrator\DayHydrator;
use eCamp\LibTest\PHPUnit\AbstractTestCase;

class DayHydratorTest extends AbstractTestCase {
    public function testExtract() {
        $camp = new Camp();
        $camp->setName('name');
        $camp->setTitle('title');
        $camp->setMotto('motto');

        $period = new Period();
        $period->setCamp($camp);
        $period->setDescription('desc');
        $period->setStart(new \DateTime('2000-01-01'));
        $period->setEnd(new \DateTime('2000-01-03'));

        $day = new Day();
        $day->setPeriod($period);
        $day->setDayOffset(0);

        $hydrator = new DayHydrator();
        $data = $hydrator->extract($day);

        $this->assertEquals(0, $data['day_offset']);
        $this->assertEquals(1, $data['number']);
    }

    public function testHydrate() {
        $camp = new Camp();

        $period = new Period();
        $period->setCamp($camp);

        $day = new Day();
        $day->setPeriod($period);

        $data = [
            'day_offset' => 0
        ];

        $hydrator = new DayHydrator();
        $hydrator->hydrate($data, $day);

        $this->assertEquals(0, $day->getDayOffset());
        $this->assertEquals(1, $day->getDayNumber());
    }
}
