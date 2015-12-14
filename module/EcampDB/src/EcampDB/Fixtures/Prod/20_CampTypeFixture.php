<?php

namespace EcampDB\Fixtures\Prod;

use EcampCore\Entity\CampType;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use EcampCore\Entity\EventType;

class CampTypeFixture extends AbstractFixture implements OrderedFixtureInterface
{
    const KINDERSPORT = 'camptype-kindersport';
    const JUGENDSPORT = 'camptype-jugendsport';
    const AUSBILDUNG = 'camptype-ausbildung';


    public function load(ObjectManager $manager)
    {
        $this->load_($manager, array(
            array(
                'name' => 'J+S Kindersport',
                'type' => 'kindersport',
                'eventTypes' => array(
                    EventTypeFixture::LAGERSPORT,
                    EventTypeFixture::LAGERAKTIVITAET,
                    EventTypeFixture::LAGERPROGRAMM
                ),
                'reference' => self::KINDERSPORT
            ),
            array(
                'name' => 'J+S Jugendsport',
                'type' => 'jugendsport',
                'eventTypes' => array(
                    EventTypeFixture::LAGERSPORT,
                    EventTypeFixture::LAGERAKTIVITAET,
                    EventTypeFixture::LAGERPROGRAMM
                ),
                'reference' => self::JUGENDSPORT
            ),
            array(
                'name' => 'J+S Ausbildung',
                'type' => 'ausbildung',
                'eventTypes' => array(
                    EventTypeFixture::LAGERSPORT,
                    EventTypeFixture::LAGERAKTIVITAET,
                    EventTypeFixture::LAGERPROGRAMM
                ),
                'reference' => self::AUSBILDUNG
            )
        ));
    }

    public function load_(ObjectManager $manager, array $config)
    {
        $campTypeRepo = $manager->getRepository('EcampCore\Entity\CampType');

        foreach($config as $campTypeConfig){
            $name = $campTypeConfig['name'];
            $type = $campTypeConfig['type'];
            $eventTypes = $campTypeConfig['eventTypes'];
            $reference = $campTypeConfig['reference'];

            /** @var CampType $campType */
            $campType = $campTypeRepo->findOneBy(array('type' => $type));

            if($campType == null){
                $campType = new CampType($name, $type);
                $manager->persist($campType);
            } else {
                $campType->setName($name);
            }


            foreach ($eventTypes as $eventType) {
                /** @var EventType $eventType */
                $eventType = $this->getReference($eventType);
                if(!$eventType->getCampTypes()->contains($campType)){
                    $eventType->getCampTypes()->add(($campType));
                }
            }

            $this->addReference($reference, $campType);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 20;
    }
}
