<?php

namespace eCamp\CoreTest\Service;

use Doctrine\Common\DataFixtures\Loader;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\MaterialList;
use eCamp\Core\Entity\User;
use eCamp\Core\EntityService\MaterialListService;
use eCamp\CoreTest\Data\CampTestData;
use eCamp\CoreTest\Data\MaterialListTestData;
use eCamp\CoreTest\Data\UserTestData;
use eCamp\Lib\Service\EntityNotFoundException;
use eCamp\LibTest\PHPUnit\AbstractApiControllerTestCase;

/**
 * @internal
 */
class MaterialListServiceTest extends AbstractApiControllerTestCase {
    /** @var User */
    protected $user;
    /** @var Camp */
    protected $camp;
    /** @var MaterialList */
    protected $materialList;

    public function setUp(): void {
        parent::setUp();

        $userLoader = new UserTestData();
        $campLoader = new CampTestData();
        $materialListLoader = new MaterialListTestData();

        $loader = new Loader();
        $loader->addFixture($userLoader);
        $loader->addFixture($campLoader);
        $loader->addFixture($materialListLoader);
        $this->loadFixtures($loader);

        $this->user = $userLoader->getReference(UserTestData::$USER1);
        $this->camp = $campLoader->getReference(CampTestData::$CAMP1);
        $this->materialList = $materialListLoader->getReference(MaterialListTestData::$MATERIALLIST1);

        $this->authenticateUser($this->user);
    }

    public function testCreateMaterialList() {
        /** @var MaterialListService $materialListService */
        $materialListService = $this->getApplicationServiceLocator()->get(MaterialListService::class);

        // Create new MaterailList
        /** @var MaterialList $materialList */
        $materialList = $materialListService->create((object) [
            'campId' => $this->camp->getId(),
            'name' => 'MaterialList',
        ]);

        // MaterialList is returned with correct data
        $this->assertNotNull($materialList);
        $this->assertEquals($this->camp, $materialList->getCamp());
        $this->assertEquals('MaterialList', $materialList->getName());

        $materialListId = $materialList->getId();

        // Load MaterialList form DB
        /** @var MaterialList $materialList */
        $materialList = $materialListService->fetch($materialListId);

        // MaterialList is returned with correct data
        $this->assertNotNull($materialList);
        $this->assertEquals($this->camp, $materialList->getCamp());
        $this->assertEquals('MaterialList', $materialList->getName());
    }

    public function testUpdateMaterialList() {
        /** @var MaterialListService $materialListService */
        $materialListService = $this->getApplicationServiceLocator()->get(MaterialListService::class);

        // Patch MaterialList
        /** @var MaterialList $materialList */
        $materialList = $materialListService->patch(
            $this->materialList->getId(),
            (object) [
                'name' => 'NewName',
            ]
        );

        // MaterialList is returned with correct data
        $this->assertNotNull($materialList);
        $this->assertEquals('NewName', $materialList->getName());

        $materialListId = $materialList->getId();

        // MaterialList is returned with correct data
        $this->assertNotNull($materialList);
        $this->assertEquals('NewName', $materialList->getName());
    }

    public function testDeleteMaterialList() {
        /** @var MaterialListService $materialListService */
        $materialListService = $this->getApplicationServiceLocator()->get(MaterialListService::class);

        // MaterialList exists:
        $materialList = $materialListService->fetch($this->materialList->getId());
        $this->assertNotNull($materialList);

        // Delete MaterialList
        $materialListService->delete($this->materialList->getId());

        // MaterialList does not exist:
        $this->expectException(EntityNotFoundException::class);
        $materialListService->fetch($this->materialList->getId());
    }
}
