<?php

namespace eCamp\CoreData;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\CampType;
use eCamp\Core\Entity\Group;
use eCamp\Core\Entity\User;

class CampData extends AbstractFixture implements DependentFixtureInterface
{
    public static $CAMP_1 = Camp::class . ':CAMP_1';
    public static $CAMP_2 = Camp::class . ':CAMP_2';

    public function load(ObjectManager $manager)
    {
        $repository = $manager->getRepository(Camp::class);

        /** @var Group $pbs */
        $pbs = $this->getReference(GroupData::$PBS);

        /** @var User $user */
        $user = $this->getReference(UserData::$USER);

        /** @var CampType $jsKidsCampType */
        $jsKidsCampType = $this->getReference(CampTypeData::$PBS_JS_KIDS);
        /** @var CampType $jsTeenCampType */
        $jsTeenCampType = $this->getReference(CampTypeData::$PBS_JS_TEEN);

        $camp = $repository->findOneBy([ 'owner' => $pbs, 'name' => 'Camp1' ]);
        if ($camp == null) {
            $camp = new Camp();
            $camp->setOwner($pbs);
            $camp->setCreator($user);
            $camp->setName('Camp1');
            $camp->setTitle('Camp1Title');
            $camp->setMotto('Camp1Motto');
            $camp->setCampType($jsKidsCampType);

            $manager->persist($camp);
        }
        $this->addReference(self::$CAMP_1, $camp);

        $camp = $repository->findOneBy([ 'owner' => $pbs, 'name' => 'Camp2' ]);
        if ($camp == null) {
            $camp = new Camp();
            $camp->setOwner($pbs);
            $camp->setCreator($user);
            $camp->setName('Camp2');
            $camp->setTitle('Camp2Title');
            $camp->setMotto('Camp2Motto');
            $camp->setCampType($jsTeenCampType);

            $manager->persist($camp);
        }
        $this->addReference(self::$CAMP_2, $camp);

        $manager->flush();
    }

    public function getDependencies()
    {
        return [ UserData::class, GroupData::class, CampTypeData::class ];
    }
}
