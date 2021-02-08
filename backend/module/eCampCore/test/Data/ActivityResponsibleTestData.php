<?php

namespace eCamp\CoreTest\Data;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use eCamp\Core\Entity\ActivityResponsible;
use eCamp\Core\Entity\CampCollaboration;

class ActivityResponsibleTestData extends AbstractFixture implements DependentFixtureInterface {
    public static $RESPONSIBLE1 = ActivityResponsible::class.':RESPONSIBLE1';

    public function load(ObjectManager $manager): void {
        /** @var Activity $activity */
        $activity = $this->getReference(ActivityTestData::$ACTIVITY1);

        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = $this->getReference(CampCollaborationTestData::$COLLAB1);

        $activityResponsible = new ActivityResponsible();
        $activityResponsible->setActivity($activity);
        $activityResponsible->setCampCollaboration($campCollaboration);

        $manager->persist($activityResponsible);
        $manager->flush();

        $this->addReference(self::$RESPONSIBLE1, $activityResponsible);
    }

    public function getDependencies() {
        return [ActivityTestData::class, CampCollaborationTestData::class];
    }
}
