<?php

namespace EcampDB\Fixtures\Test;

use EcampCore\Entity\CampType;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

class CampTypeFixture extends AbstractFixture implements OrderedFixtureInterface
{
    const KINDERSPORT = 'camptype-kindersport';
    const JUGENDSPORT = 'camptype-jugendsport';
    const AUSBILDUNG = 'camptype-ausbildung';


    public function load(ObjectManager $manager)
    {
        $repository = $manager->getRepository('EcampCore\Entity\CampType');

        $kindersport = $repository->findOneBy(array('type' => 'kindersport'));
        $jugendsport = $repository->findOneBy(array('type' => 'jugendsport'));
        $ausbildung = $repository->findOneBy(array('type' => 'ausbildung'));

        if($kindersport == null){
            $kindersport = new CampType("J+S Kindersport", "kindersport");
            $manager->persist($kindersport);
        }

        if($jugendsport == null){
            $jugendsport = new CampType("J+S Jugendsport", "jugendsport");
            $manager->persist($jugendsport);
        }

        if($ausbildung == null) {
            $ausbildung = new CampType("J+S Ausbildung", "ausbildung");
            $manager->persist($ausbildung);
        }





        /*  Add EventTypes  */
        /********************/

        $lagersport = $this->getReference(EventTypeFixture::LAGERSPORT);
        $lageraktivitaet = $this->getReference(EventTypeFixture::LAGERAKTIVITAET);
        $lagerprogramm = $this->getReference(EventTypeFixture::LAGERPROGRAMM);

        /** @var CampType $kindersport */
        if(!$kindersport->getEventTypes()->contains($lagersport)){
            $kindersport->getEventTypes()->add($lagersport);
        }
        if(!$kindersport->getEventTypes()->contains($lageraktivitaet)){
            $kindersport->getEventTypes()->add($lageraktivitaet);
        }
        if(!$kindersport->getEventTypes()->contains($lagerprogramm)){
            $kindersport->getEventTypes()->add($lagerprogramm);
        }

        /** @var CampType $jugendsport */
        if(!$jugendsport->getEventTypes()->contains($lagersport)){
            $jugendsport->getEventTypes()->add($lagersport);
        }
        if(!$jugendsport->getEventTypes()->contains($lageraktivitaet)){
            $jugendsport->getEventTypes()->add($lageraktivitaet);
        }
        if(!$jugendsport->getEventTypes()->contains($lagerprogramm)){
            $jugendsport->getEventTypes()->add($lagerprogramm);
        }

        /** @var CampType $ausbildung */
        if(!$ausbildung->getEventTypes()->contains($lagersport)){
            $ausbildung->getEventTypes()->add($lagersport);
        }
        if(!$ausbildung->getEventTypes()->contains($lageraktivitaet)){
            $ausbildung->getEventTypes()->add($lageraktivitaet);
        }
        if(!$ausbildung->getEventTypes()->contains($lagerprogramm)){
            $ausbildung->getEventTypes()->add($lagerprogramm);
        }

        $manager->flush();


        $this->addReference(self::KINDERSPORT, $kindersport);
        $this->addReference(self::JUGENDSPORT, $jugendsport);
        $this->addReference(self::AUSBILDUNG, $ausbildung);
    }

    public function getOrder()
    {
        return 10;
    }
}
