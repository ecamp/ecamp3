<?php

namespace eCamp\CoreTest\Data;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\User;

class CampTestData extends AbstractFixture implements DependentFixtureInterface {
    public static $CAMP1 = Camp::class.':CAMP1';
    public static $CAMP2 = Camp::class.':CAMP2';

    public function load(ObjectManager $manager): void {
        $this->addReference(self::$CAMP1, $this->createCamp1($manager));
        $this->addReference(self::$CAMP2, $this->createCamp2($manager));
    }

    public function getDependencies() {
        return [UserTestData::class];
    }

    /**
     * @throws \Exception
     */
    private function createCamp1(ObjectManager $manager): Camp {
        /** @var User $user */
        $user = $this->getReference(UserTestData::$USER1);

        $camp = new Camp();
        $camp->setName('CampName');
        $camp->setTitle('CampTitle');
        $camp->setMotto('CampMotto');
        $camp->setAddressName('AdrName');
        $camp->setAddressStreet('AdrStreet');
        $camp->setAddressZipcode('AdrZipcode');
        $camp->setAddressCity('AdrCity');
        $camp->setCreator($user);
        $camp->setOwner($user);

        $manager->persist($camp);
        $manager->flush();

        return $camp;
    }

    /**
     * @throws \Exception
     */
    private function createCamp2(ObjectManager $manager): Camp {
        /** @var User $user */
        $user = $this->getReference(UserTestData::$USER1);

        $camp = new Camp();
        $camp->setName('Camp2Name');
        $camp->setTitle('Camp2Title');
        $camp->setMotto('Camp2Motto');
        $camp->setAddressName('AdrName2');
        $camp->setAddressStreet('AdrStreet2');
        $camp->setAddressZipcode('AdrZipcode2');
        $camp->setAddressCity('AdrCity2');
        $camp->setCreator($user);
        $camp->setOwner($user);

        $manager->persist($camp);
        $manager->flush();

        return $camp;
    }
}
