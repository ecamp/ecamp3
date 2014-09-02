<?php
namespace EcampDB\Fixtures\Test;

use EcampCore\Entity\CampType;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

class CampTypes extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $type = new CampType("J+S Kindersport", "kindersport");
        $manager->persist($type);

        $type = new CampType("J+S Jugendsport", "jugendsport");
        $this->addReference('camptype-jugendsport', $type);
        $manager->persist($type);

        $type = new CampType("J+S Ausbildung", "ausbildung");
        $manager->persist($type);

        $manager->flush();
    }

    public function getOrder()
    {
        return 10;
    }
}
