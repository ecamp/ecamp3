<?php

namespace eCamp\ApiTest;

use DateInterval;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\Period;
use eCamp\Core\EntityService\DayService;
use eCamp\Core\EntityService\PeriodService;

class PeriodApiTest extends AbstractApiTestCase {
    function setUp() {
        parent::setUp();
        $this->createDummyData();
    }


    public function testCreatePeriod() {
        /** @var Camp $camp */
        $camp = $this->getRandomEntity(Camp::class);
        $campId = $camp->getId();
        $this->login($camp->getCreator()->getId());

        $periodCount = $camp->getPeriods()->count();

        $this->dispatchPost(
            "/api/period",
            [
                'camp_id' => $campId,
                'description' => 'test',
                'start' => '2018-10-11T00:00:00.000Z',
                'end' => '2018-10-14T00:00:00.000Z'
            ]
        );

        /** @var PeriodService $periodService */
        $periodService = $this->getService(PeriodService::class);
        $periods = $periodService->fetchAll(['where' => "row.camp = '$campId'"]);

        $this->assertEquals($periodCount + 1, $periods->getTotalItemCount());
    }

    public function testPatchPeriod1() {
        /** @var Period $period */
        $period = $this->getRandomEntity(Period::class);
        $periodId = $period->getId();
        $this->login($period->getCamp()->getCreator()->getId());

        $start = $period->getStart();
        $start->add(new DateInterval('P1D'));
        $end = $period->getEnd();
        $dayCount = $period->getDurationInDays();

        $this->dispatchPatch(
            "/api/period/" . $periodId,
            [
                'description' => 'desc_patch',
                'start' => $start,
                'end'=> $end
            ]
        );

        /** @var DayService $dayService */
        $dayService = $this->getService(DayService::class);
        $days = $dayService->fetchAll(['period_id' => $periodId]);

        $this->assertEquals($dayCount - 1, $days->getTotalItemCount());
    }

    public function testPatchPeriod2() {
        /** @var Period $period */
        $period = $this->getRandomEntity(Period::class);
        $periodId = $period->getId();
        $this->login($period->getCamp()->getCreator()->getId());

        $start = $period->getStart();
        $start->sub(new DateInterval('P1D'));
        $end = $period->getEnd();
        $end->add(new DateInterval('P1D'));
        $dayCount = $period->getDurationInDays();

        $this->dispatchPatch(
            "/api/period/" . $periodId,
            [
                'description' => 'desc_patch',
                'start' => $start,
                'end'=> $end,
                'move_events' => false
            ]
        );

        /** @var PeriodService $periodService */
        $periodService = $this->getService(PeriodService::class);
        $period = $periodService->fetch($periodId);

        $this->assertEquals('desc_patch', $period->getDescription());
        $this->assertEquals($dayCount + 2, $period->getDurationInDays());
    }

    public function testDeletePeriod() {
        /** @var Period $period */
        $period = $this->getRandomEntity(Period::class);
        $periodId = $period->getId();
        $this->login($period->getCamp()->getCreator()->getId());

        $this->dispatchDelete("/api/period/" . $periodId);

        /** @var PeriodService $periodService */
        $periodService = $this->getService(PeriodService::class);
        $this->assertNull($periodService->fetch($periodId));
    }

}
