<?php

namespace eCamp\CoreTest\Hydrator;

use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\Period;
use eCamp\Core\Hydrator\PeriodHydrator;
use eCamp\Core\Types\DateUtc;
use eCamp\LibTest\PHPUnit\AbstractTestCase;

/**
 * @internal
 */
class PeriodHydratorTest extends AbstractTestCase {
    public function testExtract() {
        $camp = new Camp();
        $camp->setName('name');
        $camp->setTitle('title');
        $camp->setMotto('motto');

        $period = new Period();
        $period->setCamp($camp);
        $period->setDescription('desc');
        $period->setStart(new DateUtc('2000-01-01'));
        $period->setEnd(new DateUtc('2000-01-03'));

        $hydrator = new PeriodHydrator();
        $data = $hydrator->extract($period);

        $this->assertEquals('desc', $data['description']);

        $this->assertEquals('2000-01-01', $data['start']);
        $this->assertEquals('2000-01-03', $data['end']);
    }

    public function testHydrate() {
        $camp = new Camp();

        $period = new Period();
        $period->setCamp($camp);

        $data = [
            'description' => 'desc',
            'start' => '2000-01-01',
            'end' => '2000-01-03',
        ];

        $hydrator = new PeriodHydrator();
        $hydrator->hydrate($data, $period);

        $this->assertEquals('desc', $period->getDescription());
        $this->assertEquals('2000-01-01', $period->getStart());
        $this->assertEquals('2000-01-03', $period->getEnd());
    }
}
