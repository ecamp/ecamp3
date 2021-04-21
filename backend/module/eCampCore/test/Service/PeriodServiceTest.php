<?php

namespace eCamp\CoreTest\Service;

use Doctrine\Common\DataFixtures\Loader;
use eCamp\Core\Entity\Period;
use eCamp\Core\EntityService\PeriodService;
use eCamp\CoreTest\Data\CampTestData;
use eCamp\CoreTest\Data\PeriodTestData;
use eCamp\CoreTest\Data\UserTestData;
use eCamp\Lib\Service\EntityValidationException;
use eCamp\LibTest\PHPUnit\AbstractApiControllerTestCase;

/**
 * @internal
 */
class PeriodServiceTest extends AbstractApiControllerTestCase {
    /** @var Period */
    protected $period;

    public function setUp(): void {
        parent::setUp();

        $userLoader = new UserTestData();
        $campLoader = new CampTestData();
        $periodLoader = new PeriodTestData();

        $loader = new Loader();
        $loader->addFixture($userLoader);
        $loader->addFixture($campLoader);
        $loader->addFixture($periodLoader);
        $this->loadFixtures($loader);

        $this->user = $userLoader->getReference(UserTestData::$USER1);
        $this->period = $periodLoader->getReference(PeriodTestData::$PERIOD1);

        $this->authenticateUser($this->user);
    }

    public function testLastPeriodNotDeletable(): void {
        /** @var PeriodService $periodService */
        $periodService = $this->getApplicationServiceLocator()->get(PeriodService::class);

        $this->expectException(EntityValidationException::class);
        $periodService->delete($this->period->getId());
    }
}
