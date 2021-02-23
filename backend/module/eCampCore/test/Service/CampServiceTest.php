<?php

namespace eCamp\CoreTest\Service;

use Doctrine\Common\DataFixtures\Loader;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\CampTemplate;
use eCamp\Core\Entity\Category;
use eCamp\Core\Entity\CategoryContent;
use eCamp\Core\Entity\MaterialList;
use eCamp\Core\Entity\User;
use eCamp\Core\EntityService\CampService;
use eCamp\CoreTest\Data\CampTemplateTestData;
use eCamp\CoreTest\Data\CategoryContentTemplateTestData;
use eCamp\CoreTest\Data\CategoryTemplateTestData;
use eCamp\CoreTest\Data\MaterialListTemplateTestData;
use eCamp\CoreTest\Data\UserTestData;
use eCamp\LibTest\PHPUnit\AbstractApiControllerTestCase;

/**
 * @internal
 */
class CampServiceTest extends AbstractApiControllerTestCase {
    /** @var User */
    protected $user;

    /** @var CampTemplate */
    protected $campTemplate;

    public function setUp(): void {
        parent::setUp();

        $userLoader = new UserTestData();
        $campTemplateLoader = new CampTemplateTestData();
        $materialListTemplateLoader = new MaterialListTemplateTestData();
        $categoryTemplateLoader = new CategoryTemplateTestData();
        $categoryContentTemplateLoader = new CategoryContentTemplateTestData();

        $loader = new Loader();
        $loader->addFixture($userLoader);
        $loader->addFixture($campTemplateLoader);
        $loader->addFixture($materialListTemplateLoader);
        $loader->addFixture($categoryTemplateLoader);
        $loader->addFixture($categoryContentTemplateLoader);
        $this->loadFixtures($loader);

        $this->user = $userLoader->getReference(UserTestData::$USER1);
        $this->campTemplate = $campTemplateLoader->getReference(CampTemplateTestData::$TEMPLATE1);

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
            'campTemplateId' => $this->campTemplate->getId(),
        ]);

        $this->assertNotNull($camp);
        $this->assertNotNull($camp->getCampTemplateId());
        $this->assertEquals('CampName', $camp->getName());

        $this->assertCount(2, $camp->getMaterialLists());
        /** @var MaterialList $materialList */
        $materialList = $camp->getMaterialLists()
            ->filter(fn ($ml) => ('MaterialListTemplate1' == $ml->getName()))
            ->first()
        ;
        $this->assertNotNull($materialList);
        $this->assertNotNull($materialList->getMaterialListTemplateId());

        $this->assertCount(1, $camp->getCategories());
        /** @var Category $category */
        $category = $camp->getCategories()->first();
        $this->assertNotNull($category);
        $this->assertNotNull($category->getCategoryTemplateId());
        $this->assertEquals('ActivityCategory1', $category->getName());

        $this->assertCount(1, $category->getCategoryContents());
        /** @var CategoryContent $categoryContent */
        $categoryContent = $category->getCategoryContents()->first();
        $this->assertNotNull($categoryContent);
        $this->assertNotNull($categoryContent->getCategoryContentTemplateId());
        $this->assertEquals('Storyboard', $categoryContent->getContentType()->getName());
    }
}
