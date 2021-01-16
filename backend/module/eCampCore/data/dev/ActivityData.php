<?php

namespace eCamp\CoreData;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use eCamp\Core\ContentType\ContentTypeStrategyProvider;
use eCamp\Core\Entity\Activity;
use eCamp\Core\Entity\ActivityCategory;
use eCamp\Core\Entity\Camp;
use eCamp\Lib\Fixture\ContainerAwareInterface;
use eCamp\Lib\Fixture\ContainerAwareTrait;

class ActivityData extends AbstractFixture implements DependentFixtureInterface, ContainerAwareInterface {
    use ContainerAwareTrait;

    public static $EVENT_1_LS = Activity::class.':EVENT_1_LS';
    public static $EVENT_1_LA = Activity::class.':EVENT_1_LA';
    public static $EVENT_2_LS = Activity::class.':EVENT_2_LS';
    public static $EVENT_2_LA = Activity::class.':EVENT_2_LA';

    public function load(ObjectManager $manager) {
        $repository = $manager->getRepository(Activity::class);

        $contentTypeStrategyProvider = $this->getContainer()->get(ContentTypeStrategyProvider::class);

        /** @var Camp $camp */
        $camp = $this->getReference(CampData::$CAMP_1);
        /** @var ActivityCategory $activityCategoryLs */
        $activityCategoryLs = $this->getReference(ActivityCategoryData::$EVENTCATEGORY_1_LS);
        /** @var ActivityCategory $activityCategoryLa */
        $activityCategoryLa = $this->getReference(ActivityCategoryData::$EVENTCATEGORY_1_LA);

        $activity = $repository->findOneBy(['camp' => $camp, 'title' => 'Activity LS']);
        if (null == $activity) {
            $activity = new Activity();
            $activity->setCamp($camp);
            $activity->setTitle('Activity LS');
            $activity->setActivityCategory($activityCategoryLs);

            $manager->persist($activity);
        }
        $this->addReference(self::$EVENT_1_LS, $activity);

        $activity = $repository->findOneBy(['camp' => $camp, 'title' => 'Activity LA']);
        if (null == $activity) {
            $activity = new Activity();
            $activity->setCamp($camp);
            $activity->setTitle('Activity LA');
            $activity->setActivityCategory($activityCategoryLa);

            $manager->persist($activity);
        }
        $this->addReference(self::$EVENT_1_LA, $activity);

        /** @var Camp $camp */
        $camp = $this->getReference(CampData::$CAMP_2);
        /** @var ActivityCategory $activityCategoryLs */
        $activityCategoryLs = $this->getReference(ActivityCategoryData::$EVENTCATEGORY_2_LS);
        /** @var ActivityCategory $activityCategoryLa */
        $activityCategoryLa = $this->getReference(ActivityCategoryData::$EVENTCATEGORY_2_LA);

        $activity = $repository->findOneBy(['camp' => $camp, 'title' => 'Activity LS']);
        if (null == $activity) {
            $activity = new Activity();
            $activity->setCamp($camp);
            $activity->setTitle('Activity LS');
            $activity->setActivityCategory($activityCategoryLs);

            $manager->persist($activity);
        }
        $this->addReference(self::$EVENT_2_LS, $activity);

        $activity = $repository->findOneBy(['camp' => $camp, 'title' => 'Activity LA']);
        if (null == $activity) {
            $activity = new Activity();
            $activity->setCamp($camp);
            $activity->setTitle('Activity LA');
            $activity->setActivityCategory($activityCategoryLa);

            $manager->persist($activity);
        }
        $this->addReference(self::$EVENT_2_LA, $activity);

        $manager->flush();
    }

    public function getDependencies() {
        return [CampData::class, ActivityCategoryData::class];
    }
}
