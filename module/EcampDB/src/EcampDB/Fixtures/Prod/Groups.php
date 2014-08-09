<?php
namespace EcampDB\Fixtures\Test;

use EcampCore\Entity\Group;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

class Groups extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $group1 = new Group();
        $group1->setName('PBS');
        $group1->setDescription('Pfadi Bewegung Schweiz');

        $group2 = new Group();
        $group2->setName('KV Luzern');
        $group2->setDescription('Kantonalverband Luzern');
        $group2->setParent($group1);

        $manager->persist($group1);
        $manager->persist($group2);
        $manager->flush();

        $this->addReference('group-pbs', $group1);
    }

    public function getOrder()
    {
        return 10;
    }
}
