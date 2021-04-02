<?php

namespace eCamp\CoreData;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use eCamp\Core\Entity\Activity;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\Category;
use eCamp\Lib\Fixture\ContainerAwareInterface;
use eCamp\Lib\Fixture\ContainerAwareTrait;

class ActivityData extends CategoryPrototypeData implements DependentFixtureInterface, ContainerAwareInterface {
    use ContainerAwareTrait;

    public static $EVENT_1_LS = Activity::class.':EVENT_1_LS';
    public static $EVENT_1_LA = Activity::class.':EVENT_1_LA';
    public static $EVENT_2_LS = Activity::class.':EVENT_2_LS';
    public static $EVENT_2_LA = Activity::class.':EVENT_2_LA';

    public function load(ObjectManager $manager): void {
        $repository = $manager->getRepository(Activity::class);

        /** @var Camp $camp */
        $camp = $this->getReference(CampData::$CAMP_1);
        /** @var Category $categoryLs */
        $categoryLs = $this->getReference(CategoryData::$EVENTCATEGORY_1_LS);
        /** @var Category $categoryLa */
        $categoryLa = $this->getReference(CategoryData::$EVENTCATEGORY_1_LA);

        $activity = $repository->findOneBy(['camp' => $camp, 'title' => 'Activity LS']);
        if (null == $activity) {
            $activity = new Activity();
            $activity->setCamp($camp);
            $activity->setTitle('Activity LS');
            $activity->setCategory($categoryLs);

            $activity->setRootContentNode($this->createInitialRootContentNode($manager));

            $manager->persist($activity);
        }
        $this->addReference(self::$EVENT_1_LS, $activity);

        $activity = $repository->findOneBy(['camp' => $camp, 'title' => 'Activity LA']);
        if (null == $activity) {
            $activity = new Activity();
            $activity->setCamp($camp);
            $activity->setTitle('Activity LA');
            $activity->setCategory($categoryLa);

            $activity->setRootContentNode($this->createInitialRootContentNode($manager));

            $manager->persist($activity);
        }
        $this->addReference(self::$EVENT_1_LA, $activity);

        /** @var Camp $camp */
        $camp = $this->getReference(CampData::$CAMP_2);
        /** @var Category $categoryLs */
        $categoryLs = $this->getReference(CategoryData::$EVENTCATEGORY_2_LS);
        /** @var Category $categoryLa */
        $categoryLa = $this->getReference(CategoryData::$EVENTCATEGORY_2_LA);

        $activity = $repository->findOneBy(['camp' => $camp, 'title' => 'Activity LS']);
        if (null == $activity) {
            $activity = new Activity();
            $activity->setCamp($camp);
            $activity->setTitle('Activity LS');
            $activity->setCategory($categoryLs);

            $activity->setRootContentNode($this->createInitialRootContentNode($manager));

            $manager->persist($activity);
        }
        $this->addReference(self::$EVENT_2_LS, $activity);

        $activity = $repository->findOneBy(['camp' => $camp, 'title' => 'Activity LA']);
        if (null == $activity) {
            $activity = new Activity();
            $activity->setCamp($camp);
            $activity->setTitle('Activity LA');
            $activity->setCategory($categoryLa);

            $activity->setRootContentNode($this->createInitialRootContentNode($manager));

            $manager->persist($activity);
        }
        $this->addReference(self::$EVENT_2_LA, $activity);

        $manager->flush();
    }

    public function getDependencies() {
        return [CampData::class, CategoryData::class, ContentTypeData::class];
    }
}
