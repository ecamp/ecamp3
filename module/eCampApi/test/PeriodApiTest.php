<?php

namespace eCamp\ApiTest;

use DateInterval;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\Period;
use eCamp\Core\Service\CampService;
use eCamp\Core\Service\PeriodService;
use eCamp\Lib\Hydrator\Util;
use eCamp\LibTest\PHPUnit\AbstractHttpControllerTestCase;
use Zend\Stdlib\Parameters;

class PeriodApiTest extends AbstractHttpControllerTestCase {
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

        /** @var \Zend\Http\PhpEnvironment\Request $request */
        $request = $this->getRequest();
        $request->setMethod('POST');
        $request->getHeaders()->addHeaderLine('Accept', 'application/json');
        $request->setPost(new Parameters([
            'camp_id' => $campId,
            'description' => 'test',
            'start' => '2018-10-11T00:00:00.000Z',
            'end' => '2018-10-14T00:00:00.000Z'
        ]));
        $this->dispatch("/api/period");

        /** @var CampService $campService */
        $campService = $this->getService(CampService::class);
        /** @var Camp $camp */
        $camp = $campService->fetch($campId);

        $this->assertEquals($periodCount + 1, $camp->getPeriods()->count());
    }

    public function testPatchPeriod() {
        /** @var Period $period */
        $period = $this->getRandomEntity(Period::class);
        $periodId = $period->getId();
        $this->login($period->getCamp()->getCreator()->getId());

        $start = $period->getStart();
        $end = $period->getEnd();
        $end->add(new DateInterval('P1D'));
        $days = $period->getDurationInDays();

        /** @var \Zend\Http\PhpEnvironment\Request $request */
        $request = $this->getRequest();
        $request->setMethod('PATCH');
        $request->getHeaders()->addHeaderLine('Accept', 'application/json');
        $request->setContent(json_encode([
            'description' => 'desc_patch',
            'start' => $start,
            'end'=> $end
        ]));
        $this->dispatch("/api/period/" . $periodId);


        /** @var PeriodService $periodService */
        $periodService = $this->getService(PeriodService::class);
        $period = $periodService->fetch($periodId);

        $this->assertEquals('desc_patch', $period->getDescription());
        $this->assertEquals($days + 1, $period->getDurationInDays());
    }

    public function testDeletePeriod() {
        /** @var Period $period */
        $period = $this->getRandomEntity(Period::class);
        $periodId = $period->getId();
        $this->login($period->getCamp()->getCreator()->getId());

        /** @var \Zend\Http\PhpEnvironment\Request $request */
        $request = $this->getRequest();
        $request->setMethod('DELETE');
        $request->getHeaders()->addHeaderLine('Accept', 'application/json');
        $this->dispatch("/api/period/" . $periodId);

        /** @var PeriodService $periodService */
        $periodService = $this->getService(PeriodService::class);
        $this->assertNull($periodService->fetch($periodId));
    }

}
