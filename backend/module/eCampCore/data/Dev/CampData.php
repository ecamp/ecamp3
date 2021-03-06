<?php

namespace eCamp\CoreData\Dev;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\User;

class CampData extends AbstractFixture implements DependentFixtureInterface {
    public static $CAMP_1 = Camp::class.':CAMP_1';
    public static $CAMP_2 = Camp::class.':CAMP_2';

    public function load(ObjectManager $manager): void {
        $repository = $manager->getRepository(Camp::class);

        /** @var User $user */
        $user = $this->getReference(UserData::$USER);

        $camp = $repository->findOneBy(['owner' => $user, 'name' => 'Camp 1']);
        if (null == $camp) {
            $camp = new Camp();
            $camp->setOwner($user);
            $camp->setCreator($user);
            $camp->setName('Camp 1');
            $camp->setTitle('Camp 1 Title');
            $camp->setMotto('Camp 1 Motto');

            $manager->persist($camp);
        }
        $this->addReference(self::$CAMP_1, $camp);

        $camp = $repository->findOneBy(['owner' => $user, 'name' => 'Camp 2']);
        if (null == $camp) {
            $camp = new Camp();
            $camp->setOwner($user);
            $camp->setCreator($user);
            $camp->setName('Camp 2');
            $camp->setTitle('Camp 2 Title');
            $camp->setMotto('Camp 2 Motto');

            $manager->persist($camp);
        }
        $this->addReference(self::$CAMP_2, $camp);

        $manager->flush();
    }

    public function getDependencies() {
        return [UserData::class];
    }
}
