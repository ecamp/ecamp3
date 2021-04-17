<?php

namespace eCamp\CoreData\Prod;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\User;

class CampPrototypeData extends AbstractFixture implements DependentFixtureInterface {
    public static $PBS_JS_KIDS = Camp::class.':PBS_JS_KIDS';
    public static $PBS_JS_TEEN = Camp::class.':PBS_JS_TEEN';

    public function load(ObjectManager $manager): void {
        $repository = $manager->getRepository(Camp::class);

        /** @var User $admin */
        $admin = $this->getReference(AdminData::$ADMIN);

        /** @var Camp $camp */
        $camp = $repository->findOneBy(['name' => 'J+S Kids']);
        if (null == $camp) {
            $camp = new Camp();
            $camp->setIsPrototype(true);
            $camp->setName('J+S Kids');
            $camp->setTitle('J+S Kids');
            $camp->setCreator($admin);
            $camp->setOwner($admin);

            $manager->persist($camp);
        }
        $this->addReference(self::$PBS_JS_KIDS, $camp);

        /** @var Camp $camp */
        $camp = $repository->findOneBy(['name' => 'J+S Teen']);
        if (null == $camp) {
            $camp = new Camp();
            $camp->setIsPrototype(true);
            $camp->setName('J+S Teen');
            $camp->setTitle('J+S Teen');
            $camp->setCreator($admin);
            $camp->setOwner($admin);

            $manager->persist($camp);
        }
        $this->addReference(self::$PBS_JS_TEEN, $camp);

        $manager->flush();
    }

    public function getDependencies() {
        return [AdminData::class];
    }
}
