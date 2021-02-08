<?php

namespace eCamp\CoreData;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use eCamp\Core\Entity\Group;

class GroupData extends AbstractFixture implements DependentFixtureInterface {
    public static $PBS = Group::class.':PBS';
    public static $PFADI_LUZERN = Group::class.':PBS:PfadiLuzern';
    public static $PFADI_BASEL = Group::class.':PBS:PfadiBasel';

    public function load(ObjectManager $manager): void {
        // disable group code
        return;
        $repository = $manager->getRepository(Group::class);

        $pbs = $this->getReference(OrganizationData::$PBS);

        $group = $repository->findOneBy([
            'parent' => null,
            'name' => 'Pfadi Bewegung Schweiz',
        ]);
        if (null == $group) {
            $group = new Group();
            $group->setParent(null);
            $group->setName('Pfadi Bewegung Schweiz');
            $group->setOrganization($pbs);
            $group->setDescription('Pfadi Bewegung Schweiz');

            $manager->persist($group);
        }
        $this->addReference(self::$PBS, $group);

        $group = $repository->findOneBy([
            'parent' => $this->getReference(self::$PBS),
            'name' => 'Pfadi Luzern',
        ]);
        if (null == $group) {
            $group = new Group();
            $group->setParent($this->getReference(self::$PBS));
            $group->setName('Pfadi Luzern');
            $group->setOrganization($pbs);
            $group->setDescription('Pfadi Luzern');

            $manager->persist($group);
        }
        $this->addReference(self::$PFADI_LUZERN, $group);

        $group = $repository->findOneBy([
            'parent' => $this->getReference(self::$PBS),
            'name' => 'Pfadi Region Basel',
        ]);
        if (null == $group) {
            $group = new Group();
            $group->setParent($this->getReference(self::$PBS));
            $group->setName('Pfadi Region Basel');
            $group->setOrganization($pbs);
            $group->setDescription('Pfadi Region Basel');

            $manager->persist($group);
        }
        $this->addReference(self::$PFADI_BASEL, $group);

        $manager->flush();
    }

    public function getDependencies() {
        return [OrganizationData::class];
    }
}
