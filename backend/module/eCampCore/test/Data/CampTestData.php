<?php

namespace eCamp\CoreTest\Data;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\CampType;
use eCamp\Core\Entity\User;

class CampTestData extends AbstractFixture implements DependentFixtureInterface {
    public static $CAMP1 = Camp::class.':CAMP1';

    public function load(ObjectManager $manager) {
        $repository = $manager->getRepository(Camp::class);

        /** @var User $user */
        $user1 = $this->getReference(UserTestData::$USER1);

        /** @var CampType $campType1 */
        $campType1 = $this->getReference(CampTypeTestData::$TYPE1);

        $camp = new Camp();
        $camp->setName('CampName');
        $camp->setTitle('CampTitle');
        $camp->setMotto('CampMotto');
        $camp->setCampType($campType1);
        $camp->setCreator($user1);
        $camp->setOwner($user1);

        $manager->persist($camp);
        $manager->flush();

        $this->addReference(self::$CAMP1, $camp);
    }

    public function getDependencies() {
        return [UserTestData::class, CampTypeTestData::class];
    }
}
