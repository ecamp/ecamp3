<?php

namespace eCamp\CoreTest\Service;

use Doctrine\Common\DataFixtures\Loader;
use eCamp\Core\Entity\Activity;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\Category;
use eCamp\Core\Entity\ContentNode;
use eCamp\Core\Entity\User;
use eCamp\Core\EntityService\ActivityService;
use eCamp\CoreTest\Data\CampTestData;
use eCamp\CoreTest\Data\CategoryContentTestData;
use eCamp\CoreTest\Data\CategoryContentTypeTestData;
use eCamp\CoreTest\Data\CategoryTestData;
use eCamp\CoreTest\Data\UserTestData;
use eCamp\LibTest\PHPUnit\AbstractApiControllerTestCase;

/**
 * @internal
 */
class ActivityServiceTest extends AbstractApiControllerTestCase {
    /** @var User */
    protected $user;

    /** @var Camp */
    protected $camp;

    /** @var Category */
    protected $category;

    public function setUp(): void {
        parent::setUp();

        $userLoader = new UserTestData();
        $campLoader = new CampTestData();
        $categoryLoader = new CategoryTestData();
        $categoryContentTypeLoader = new CategoryContentTypeTestData();
        $categoryContentLoader = new CategoryContentTestData();

        $loader = new Loader();
        $loader->addFixture($userLoader);
        $loader->addFixture($campLoader);
        $loader->addFixture($categoryLoader);
        $loader->addFixture($categoryContentTypeLoader);
        $loader->addFixture($categoryContentLoader);
        $this->loadFixtures($loader);

        $this->user = $userLoader->getReference(UserTestData::$USER1);
        $this->camp = $campLoader->getReference(CampTestData::$CAMP1);
        $this->category = $categoryLoader->getReference(CategoryTestData::$CATEGORY1);

        $this->authenticateUser($this->user);
    }

    public function testCreateActivity(): void {
        /** @var ActivityService $activityService */
        $activityService = $this->getApplicationServiceLocator()->get(ActivityService::class);

        $this->assertCount(1, $this->category->getCategoryContents());
        $this->assertCount(1, $this->category->getCategoryContentTypes());

        /** @var Activity $activity */
        $activity = $activityService->create((object) [
            'title' => 'ActivityTitle',
            'campId' => $this->camp->getId(),
            'categoryId' => $this->category->getId(),
        ]);

        $this->assertNotNull($activity);
        $this->assertEquals('ActivityTitle', $activity->getTitle());

        $this->assertCount(1, $activity->getContentNodes());
        /** @var ContentNode $contentNode */
        $contentNode = $activity->getContentNodes()->first();
        $this->assertEquals('Storyboard', $contentNode->getContentType()->getName());
    }
}
