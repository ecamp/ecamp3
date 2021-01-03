<?php

namespace eCamp\CoreTest\Service;

use Doctrine\Common\DataFixtures\Loader;
use eCamp\Core\Entity\MaterialItem;
use eCamp\Core\Entity\MaterialList;
use eCamp\Core\Entity\Period;
use eCamp\Core\Entity\User;
use eCamp\Core\EntityService\MaterialItemService;
use eCamp\CoreTest\Data\MaterialListTestData;
use eCamp\CoreTest\Data\PeriodTestData;
use eCamp\CoreTest\Data\UserTestData;
use eCamp\Lib\Service\EntityValidationException;
use eCamp\LibTest\PHPUnit\AbstractDatabaseTestCase;
use Laminas\Authentication\AuthenticationService;

/**
 * @internal
 */
class MaterialItemServiceTest extends AbstractDatabaseTestCase {
    /** @var User */
    protected $user;

    /** @var MaterialList */
    protected $list;

    /** @var Period */
    protected $period;

    public function setUp(): void {
        parent::setUp();

        $userLoader = new UserTestData();
        $materialListLoader = new MaterialListTestData();
        $periodLoader = new PeriodTestData();

        $loader = new Loader();
        $loader->addFixture($userLoader);
        $loader->addFixture($materialListLoader);
        $loader->addFixture($periodLoader);
        $this->loadFixtures($loader);

        $this->user = $userLoader->getReference(UserTestData::$USER1);
        $this->list = $materialListLoader->getReference(MaterialListTestData::$MATERIALLIST1);
        $this->period = $periodLoader->getReference(PeriodTestData::$PERIOD1);

        $authenticationService = new AuthenticationService();
        $authenticationService->getStorage()->write($this->user->getId());
    }

    public function testAddPeriodItem() {
        /** @var MaterialItemService $materialItemService */
        $materialItemService = \eCampApp::GetService(MaterialItemService::class);

        /** @var MaterialItem $item */
        $item = $materialItemService->create((object) [
            'materialListId' => $this->list->getId(),
            'periodId' => $this->period->getId(),
            'quantity' => 5,
            'unit' => 'Stk',
            'article' => 'Article',
        ]);

        $this->assertEquals(5, $item->getQuantity());
        $this->assertEquals('Stk', $item->getUnit());
        $this->assertEquals('Article', $item->getArticle());
        $this->assertEquals($this->list, $item->getMaterialList());
    }

    public function testAddInvalidItem() {
        /** @var MaterialItemService $materialItemService */
        $materialItemService = \eCampApp::GetService(MaterialItemService::class);

        // Period or ActivityContent is required
        $this->expectException(EntityValidationException::class);

        $materialItemService->create((object) [
            'materialListId' => $this->list->getId(),
            'quantity' => 5,
            'unit' => 'Stk',
            'article' => 'Article',
        ]);
    }
}
