<?php

namespace eCamp\CoreData;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use eCamp\Core\Entity\Group;

class GroupData extends AbstractFixture implements DependentFixtureInterface
{
    public static $PBS = Group::class . ':PBS';


    public function load(ObjectManager $manager) {
        $repository = $manager->getRepository(Group::class);

        $pbs = $this->getReference(OrganizationData::$PBS);


        $group =  $repository->findOneBy([ 'name' => 'J+S Kids' ]);
        if ($group == null) {
            $group = new Group();
            $group->setParent(null);
            $group->setName('PBS');
            $group->setOrganization($pbs);
            $group->setDescription('Pfadi Bewegung Schweiz');

            $manager->persist($group);
        }
        $this->addReference(self::$PBS, $group);


        $manager->flush();
    }

    function getDependencies() {
        return [ OrganizationData::class ];
    }

}
