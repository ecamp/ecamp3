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
use eCamp\CoreTest\Data\CategoryContentTypeTestData;
use eCamp\CoreTest\Data\CategoryTestData;
use eCamp\CoreTest\Data\ContentNodeTestData;
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
        $container = $this->getApplicationServiceLocator();

        $userLoader = new UserTestData();
        $campLoader = new CampTestData();
        $categoryLoader = new CategoryTestData();
        $categoryContentTypeLoader = new CategoryContentTypeTestData();
        $contentNodeLoader = new ContentNodeTestData($container);

        $loader = new Loader();
        $loader->addFixture($userLoader);
        $loader->addFixture($campLoader);
        $loader->addFixture($categoryLoader);
        $loader->addFixture($categoryContentTypeLoader);
        $loader->addFixture($contentNodeLoader);
        $this->loadFixtures($loader);

        $this->user = $userLoader->getReference(UserTestData::$USER1);
        $this->camp = $campLoader->getReference(CampTestData::$CAMP1);
        $this->category = $categoryLoader->getReference(CategoryTestData::$CATEGORY1);

        $this->authenticateUser($this->user);
    }

    public function testCreateActivity(): void {
        /** @var ActivityService $activityService */
        $activityService = $this->getApplicationServiceLocator()->get(ActivityService::class);

        $this->assertCount(1, $this->category->getPreferredContentTypes());

        /** @var Activity $activity */
        $activity = $activityService->create((object) [
            'title' => 'ActivityTitle',
            'campId' => $this->camp->getId(),
            'categoryId' => $this->category->getId(),
        ]);

        $this->assertNotNull($activity);
        $this->assertEquals('ActivityTitle', $activity->getTitle());

        $this->assertCount(1, $activity->getAllContentNodes());
        /** @var ContentNode $contentNode */
        $contentNode = $activity->getAllContentNodes()->first();
        $this->assertEquals('Storyboard', $contentNode->getContentType()->getName());
    }
}
