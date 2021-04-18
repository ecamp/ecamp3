<?php

namespace eCamp\CoreTest\Data;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\CampCollaboration;
use eCamp\Core\Entity\User;
use RuntimeException;

class CampCollaborationTestData extends AbstractFixture implements DependentFixtureInterface {
    public static $COLLAB1 = CampCollaboration::class.':COLLAB1';
    public static $COLLAB_GUEST = CampCollaboration::class.':COLLAB_GUEST';
    public static $COLLAB_MANAGER = CampCollaboration::class.':COLLAB_MANAGER';
    public static $COLLAB_INVITED = CampCollaboration::class.':COLLAB_INVITED';
    public static $COLLAB_INVITED_AS_GUEST = CampCollaboration::class.':COLLAB_INVITED_AS_GUEST';
    public static $COLLAB_INACTIVE = CampCollaboration::class.':COLLAB_INACTIVE';

    public function load(ObjectManager $manager): void {
        /** @var Camp $camp */
        $camp = $this->getReference(CampTestData::$CAMP1);
        $this->addReference(
            self::$COLLAB1,
            $this->createCampCollaboration(
                $manager,
                $camp,
                UserTestData::$USER1,
                CampCollaboration::ROLE_MEMBER,
                CampCollaboration::STATUS_ESTABLISHED
            )
        );

        $this->addReference(
            self::$COLLAB_GUEST,
            $this->createCampCollaboration(
                $manager,
                $camp,
                UserTestData::$USER3,
                CampCollaboration::ROLE_GUEST,
                CampCollaboration::STATUS_ESTABLISHED
            )
        );

        $this->addReference(
            self::$COLLAB_MANAGER,
            $this->createCampCollaboration(
                $manager,
                $camp,
                UserTestData::$USER4,
                CampCollaboration::ROLE_MANAGER,
                CampCollaboration::STATUS_ESTABLISHED
            )
        );

        $this->addReference(
            self::$COLLAB_INVITED,
            $this->createCampCollaboration(
                $manager,
                $camp,
                null,
                CampCollaboration::ROLE_MEMBER,
                CampCollaboration::STATUS_INVITED,
                'e.mail@test.com',
                'myInviteKey'
            )
        );

        $this->addReference(
            self::$COLLAB_INVITED_AS_GUEST,
            $this->createCampCollaboration(
                $manager,
                $camp,
                null,
                CampCollaboration::ROLE_GUEST,
                CampCollaboration::STATUS_INVITED,
                'e.mail.guest@test.com',
                'myInviteKeyGuest'
            )
        );

        $this->addReference(
            self::$COLLAB_INACTIVE,
            $this->createCampCollaboration(
                $manager,
                $camp,
                null,
                CampCollaboration::ROLE_MEMBER,
                CampCollaboration::STATUS_INACTIVE,
                'e.mail.inactive@test.com'
            )
        );
    }

    public function getDependencies() {
        return [CampTestData::class, UserTestData::class];
    }

    private function createCampCollaboration(
        ObjectManager $manager,
        Camp $camp,
        ?string $userReference,
        string $role,
        string $status,
        ?string $inviteEmail = null,
        ?string $inviteKey = null
    ): CampCollaboration {
        $campCollaboration = new CampCollaboration();

        if (null != $userReference) {
            /** @var User $user */
            $user = $this->getReference($userReference);

            $campCollaboration->setUser($user);
        }

        $campCollaboration->setCamp($camp);

        try {
            $campCollaboration->setRole($role);
            $campCollaboration->setStatus($status);
        } catch (\Exception $e) {
            throw new RuntimeException($e);
        }
        $campCollaboration->setInviteEmail($inviteEmail);
        $campCollaboration->setInviteKey($inviteKey);

        $manager->persist($campCollaboration);
        $manager->flush();

        return $campCollaboration;
    }
}
