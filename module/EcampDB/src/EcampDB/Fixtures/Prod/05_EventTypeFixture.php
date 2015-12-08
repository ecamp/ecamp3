<?php
namespace EcampDB\Fixtures\Test;

use EcampCore\Entity\EventType;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

class EventTypeFixture extends AbstractFixture implements OrderedFixtureInterface
{
    const LAGERSPORT = 'eventtype-lagersport';
    const LAGERAKTIVITAET = 'eventtype-lageraktivitaet';
    const LAGERPROGRAMM = 'eventtype-lagerprogramm';


    public function load(ObjectManager $manager)
    {
        $repository = $manager->getRepository('EcampCore\Entity\EventType');

        $lagersport = $repository->findOneBy(array('name' => 'Lagersport'));
        $lageraktivitaet = $repository->findOneBy(array('name' => 'Lageraktivität'));
        $lagerprogramm = $repository->findOneBy(array('name' => 'Lagerprogramm'));

        if($lagersport == null){
            $lagersport = new EventType('Lagersport', '#ff5555', 'a');
            $manager->persist($lagersport);
        }

        if($lageraktivitaet == null){
            $lageraktivitaet = new EventType('Lageraktivität', '#55ff55', '1');
            $manager->persist($lageraktivitaet);
        }

        if($lagerprogramm == null){
            $lagerprogramm = new EventType('Lagerprogramm', '#00ffff', 'i');
            $manager->persist($lagerprogramm);
        }

        $manager->flush();

        $this->addReference(self::LAGERSPORT, $lagersport);
        $this->addReference(self::LAGERAKTIVITAET, $lageraktivitaet);
        $this->addReference(self::LAGERPROGRAMM, $lagerprogramm);

    }

    public function getOrder()
    {
        return 5;
    }
}
