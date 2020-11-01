<?php

namespace eCamp\CoreTest\Data;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\CampCollaboration;
use eCamp\Core\Entity\User;

class CampCollaborationTestData extends AbstractFixture implements DependentFixtureInterface {
    public static $COLLAB1 = CampCollaboration::class.':COLLAB1';

    public function load(ObjectManager $manager) {
        /** @var Camp $camp */
        $camp = $this->getReference(CampTestData::$CAMP1);

        /** @var User $user */
        $user = $this->getReference(UserTestData::$USER1);

        $campCollaboration = new CampCollaboration();
        $campCollaboration->setCamp($camp);
        $campCollaboration->setUser($user);
        $campCollaboration->setRole(CampCollaboration::ROLE_MEMBER);
        $campCollaboration->setStatus(CampCollaboration::STATUS_ESTABLISHED);

        $manager->persist($campCollaboration);
        $manager->flush();

        $this->addReference(self::$COLLAB1, $campCollaboration);
    }

    public function getDependencies() {
        return [CampTestData::class, UserTestData::class];
    }
}
