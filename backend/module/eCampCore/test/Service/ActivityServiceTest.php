<?php

namespace eCamp\CoreTest\Service;

use Doctrine\Common\DataFixtures\Loader;
use eCamp\Core\Entity\Activity;
use eCamp\Core\Entity\ActivityCategory;
use eCamp\Core\Entity\ActivityContent;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\User;
use eCamp\Core\EntityService\ActivityService;
use eCamp\CoreTest\Data\ActivityCategoryTestData;
use eCamp\CoreTest\Data\CampTestData;
use eCamp\CoreTest\Data\ContentTypeConfigTestData;
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

    /** @var ActivityCategory */
    protected $activityCategory;

    public function setUp(): void {
        parent::setUp();

        $userLoader = new UserTestData();
        $campLoader = new CampTestData();
        $activityCategoryLoader = new ActivityCategoryTestData();
        $contentTypeConfigLoader = new ContentTypeConfigTestData();

        $loader = new Loader();
        $loader->addFixture($userLoader);
        $loader->addFixture($campLoader);
        $loader->addFixture($activityCategoryLoader);
        $loader->addFixture($contentTypeConfigLoader);
        $this->loadFixtures($loader);

        $this->user = $userLoader->getReference(UserTestData::$USER1);
        $this->camp = $campLoader->getReference(CampTestData::$CAMP1);
        $this->activityCategory = $activityCategoryLoader->getReference(ActivityCategoryTestData::$CATEGORY1);

        $this->authenticateUser($this->user);
    }

    public function testCreateActivity() {
        /** @var ActivityService $activityService */
        $activityService = $this->getApplicationServiceLocator()->get(ActivityService::class);

        $this->assertCount(1, $this->activityCategory->getContentTypeConfigs());

        /** @var Activity $activity */
        $activity = $activityService->create((object) [
            'title' => 'ActivityTitle',
            'campId' => $this->camp->getId(),
            'activityCategoryId' => $this->activityCategory->getId(),
        ]);

        $this->assertNotNull($activity);
        $this->assertEquals('ActivityTitle', $activity->getTitle());

        $this->assertCount(1, $activity->getActivityContents());
        /** @var ActivityContent $activityContent */
        $activityContent = $activity->getActivityContents()->first();
        $this->assertEquals('Storyboard', $activityContent->getContentType()->getName());
    }
}
