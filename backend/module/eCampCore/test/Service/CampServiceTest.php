<?php

namespace eCamp\CoreTest\Service;

use Doctrine\Common\DataFixtures\Loader;
use eCamp\Core\Entity\ActivityCategory;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\CampTemplate;
use eCamp\Core\Entity\ContentTypeConfig;
use eCamp\Core\Entity\MaterialList;
use eCamp\Core\Entity\User;
use eCamp\Core\EntityService\CampService;
use eCamp\CoreTest\Data\ActivityCategoryTemplateTestData;
use eCamp\CoreTest\Data\CampTemplateTestData;
use eCamp\CoreTest\Data\ContentTypeConfigTemplateTestData;
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
        $activityCategoryTemplateLoader = new ActivityCategoryTemplateTestData();
        $contentTypeConfigTemplateLoader = new ContentTypeConfigTemplateTestData();

        $loader = new Loader();
        $loader->addFixture($userLoader);
        $loader->addFixture($campTemplateLoader);
        $loader->addFixture($materialListTemplateLoader);
        $loader->addFixture($activityCategoryTemplateLoader);
        $loader->addFixture($contentTypeConfigTemplateLoader);
        $this->loadFixtures($loader);

        $this->user = $userLoader->getReference(UserTestData::$USER1);
        $this->campTemplate = $campTemplateLoader->getReference(CampTemplateTestData::$TYPE1);

        $this->authenticateUser($this->user);
    }

    public function testCreateCampFromTemplate() {
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

        $this->assertCount(1, $camp->getActivityCategories());
        /** @var ActivityCategory $activityCategory */
        $activityCategory = $camp->getActivityCategories()->first();
        $this->assertNotNull($activityCategory);
        $this->assertNotNull($activityCategory->getActivityCategoryTemplateId());
        $this->assertEquals('ActivityType1', $activityCategory->getName());

        $this->assertCount(1, $activityCategory->getContentTypeConfigs());
        /** @var ContentTypeConfig $contentTypeConfig */
        $contentTypeConfig = $activityCategory->getContentTypeConfigs()->first();
        $this->assertNotNull($contentTypeConfig);
        $this->assertNotNull($contentTypeConfig->getContentTypeConfigTemplateId());
        $this->assertEquals('Storyboard', $contentTypeConfig->getContentType()->getName());
    }
}
