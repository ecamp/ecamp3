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
    public static $COLLAB_INVITED = CampCollaboration::class.':COLLAB_INVITED';
    public static $COLLAB_LEFT = CampCollaboration::class.':COLLAB_LEFT';

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

        $campCollaborationInvited = new CampCollaboration();
        $campCollaborationInvited->setCamp($camp);
        $campCollaborationInvited->setInviteEmail('e.mail@test.com');
        $campCollaborationInvited->setRole(CampCollaboration::ROLE_GUEST);
        $campCollaborationInvited->setStatus(CampCollaboration::STATUS_INVITED);
        $campCollaborationInvited->setInviteKey('myInviteKey');

        $manager->persist($campCollaborationInvited);
        $manager->flush();

        $this->addReference(self::$COLLAB_INVITED, $campCollaborationInvited);

        $campCollaborationLeft = new CampCollaboration();
        $campCollaborationLeft->setCamp($camp);
        $campCollaborationLeft->setInviteEmail('e.mail.left@test.com');
        $campCollaborationLeft->setRole(CampCollaboration::ROLE_GUEST);
        $campCollaborationLeft->setStatus(CampCollaboration::STATUS_LEFT);

        $manager->persist($campCollaborationLeft);
        $manager->flush();

        $this->addReference(self::$COLLAB_LEFT, $campCollaborationLeft);
    }

    public function getDependencies() {
        return [CampTestData::class, UserTestData::class];
    }
}
