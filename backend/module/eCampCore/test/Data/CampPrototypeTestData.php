<?php

namespace eCamp\CoreTest\Data;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\User;

class CampPrototypeTestData extends AbstractFixture implements DependentFixtureInterface {
    public static $PROTOTYPE1 = Camp::class.':Prototype1';

    public function load(ObjectManager $manager): void {
        /** @var User $admin */
        $admin = $this->getReference(AdminTestData::$ADMIN);

        $camp = new Camp();
        $camp->setName('CampPrototype1');
        $camp->setTitle('CampPrototype1');
        $camp->setCreator($admin);
        $camp->setOwner($admin);
        $camp->setIsTemplate(true);

        $manager->persist($camp);
        $manager->flush();

        $this->addReference(self::$PROTOTYPE1, $camp);
    }

    public function getDependencies() {
        return [AdminTestData::class];
    }
}
