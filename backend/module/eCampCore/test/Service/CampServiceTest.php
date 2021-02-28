<?php

namespace eCamp\CoreTest\Service;

use Doctrine\Common\DataFixtures\Loader;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\Category;
use eCamp\Core\Entity\MaterialList;
use eCamp\Core\Entity\User;
use eCamp\Core\EntityService\CampService;
use eCamp\CoreTest\Data\CampPrototypeTestData;
use eCamp\CoreTest\Data\CategoryContentTypePrototypeTestData;
use eCamp\CoreTest\Data\CategoryPrototypeTestData;
use eCamp\CoreTest\Data\ContentNodePrototypeTestData;
use eCamp\CoreTest\Data\MaterialListPrototypeTestData;
use eCamp\CoreTest\Data\UserTestData;
use eCamp\LibTest\PHPUnit\AbstractApiControllerTestCase;

/**
 * @internal
 */
class CampServiceTest extends AbstractApiControllerTestCase {
    /** @var User */
    protected $user;

    /** @var Camp */
    protected $campPrototype;

    public function setUp(): void {
        parent::setUp();
        $container = $this->getApplicationServiceLocator();

        $userLoader = new UserTestData();
        $campPrototypeLoader = new CampPrototypeTestData();
        $materialListPrototypeLoader = new MaterialListPrototypeTestData();
        $categoryPrototypeLoader = new CategoryPrototypeTestData();
        $categoryContentTypePrototypeLoader = new CategoryContentTypePrototypeTestData();
        $contentNodePrototypeLoader = new ContentNodePrototypeTestData($container);

        $loader = new Loader();
        $loader->addFixture($userLoader);
        $loader->addFixture($campPrototypeLoader);
        $loader->addFixture($materialListPrototypeLoader);
        $loader->addFixture($categoryPrototypeLoader);
        $loader->addFixture($categoryContentTypePrototypeLoader);
        $loader->addFixture($contentNodePrototypeLoader);
        $this->loadFixtures($loader);

        $this->user = $userLoader->getReference(UserTestData::$USER1);
        $this->campPrototype = $campPrototypeLoader->getReference(CampPrototypeTestData::$PROTOTYPE1);

        $this->authenticateUser($this->user);
    }

    public function testCreateCampFromTemplate(): void {
        /** @var CampService $campService */
        $campService = $this->getApplicationServiceLocator()->get(CampService::class);

        /** @var Camp $camp */
        $camp = $campService->create((object) [
            'name' => 'CampName',
            'title' => 'CampTitle',
            'motto' => 'CampMotto',
            'campPrototypeId' => $this->campPrototype->getId(),
        ]);

        $this->assertNotNull($camp);
        $this->assertNotNull($camp->getCampPrototypeId());
        $this->assertEquals('CampName', $camp->getName());

        $this->assertCount(2, $camp->getMaterialLists());
        /** @var MaterialList $materialList */
        $materialList = $camp->getMaterialLists()
            ->filter(fn ($ml) => ('MaterialListPrototype1' == $ml->getName()))
            ->first()
        ;
        $this->assertNotNull($materialList);
        $this->assertNotNull($materialList->getMaterialListPrototypeId());

        $this->assertCount(1, $camp->getCategories());
        /** @var Category $category */
        $category = $camp->getCategories()->first();
        $this->assertNotNull($category);
        $this->assertNotNull($category->getCategoryPrototypeId());
        $this->assertEquals('ActivityCategory1', $category->getName());

        // CategoryContentTypes have been copied
        $this->assertCount(1, $category->getCategoryContentTypes());

        // ContentNode have been copied
        $this->assertNotNull($category->getRootContentNode());
    }
}
