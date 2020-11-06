<?php

namespace eCamp\CoreTest\Hydrator;

use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\Period;
use eCamp\Core\Hydrator\PeriodHydrator;
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
        $period->setStart(new \DateTime('2000-01-01'));
        $period->setEnd(new \DateTime('2000-01-03'));

        $hydrator = new PeriodHydrator();
        $data = $hydrator->extract($period);

        $this->assertEquals('desc', $data['description']);

        // TODO: Top my opinion this should return a date and not a datetime
        $this->assertEquals('2000-01-01T00:00:00+00:00', $data['start']);
        $this->assertEquals('2000-01-03T23:59:59+00:00', $data['end']);
    }

    public function testHydrate() {
        $camp = new Camp();

        $period = new Period();
        $period->setCamp($camp);

        $data = [
            'description' => 'desc',
            'start' => '2000-01-01T00:00:00+00:00',
            'end' => '2000-01-03T23:59:59+00:00',
        ];

        $hydrator = new PeriodHydrator();
        $hydrator->hydrate($data, $period);

        $this->assertEquals('desc', $period->getDescription());
        $this->assertEquals('2000-01-01', $period->getStart()->format('Y-m-d'));
        $this->assertEquals('2000-01-03', $period->getEnd()->format('Y-m-d'));
    }
}
