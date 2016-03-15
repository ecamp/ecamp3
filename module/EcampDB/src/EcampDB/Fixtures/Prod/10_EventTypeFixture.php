<?php
namespace EcampDB\Fixtures\Prod;

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
        $this->load_($manager, array(
            array(
                'name' => 'Lagersport',
                'defaultColor' => '#ff5555',
                'defaultNumberingStyle' => 'a',
                'reference' => self::LAGERSPORT
            ),
            array(
                'name' => 'Lageraktivität',
                'defaultColor' => '#55ff55',
                'defaultNumberingStyle' => '1',
                'reference' => self::LAGERAKTIVITAET
            ),
            array(
                'name' => 'Lagerprogramm',
                'defaultColor' => '#00ffff',
                'defaultNumberingStyle' => 'i',
                'reference' => self::LAGERPROGRAMM
            )
        ));
    }

    private function load_(ObjectManager $manager, array $config)
    {
        $eventTypeRepo = $manager->getRepository('EcampCore\Entity\EventType');

        foreach($config as $eventTypeConfig){
            $name = $eventTypeConfig['name'];
            $color = $eventTypeConfig['defaultColor'];
            $numberingStyle = $eventTypeConfig['defaultNumberingStyle'];
            $reference = $eventTypeConfig['reference'];

            /** @var EventType $eventType */
            $eventType = $eventTypeRepo->findOneBy(array('name' => $name));

            if($eventType == null){
                $eventType = new EventType($name, $color, $numberingStyle);
                $manager->persist($eventType);
            } else {
                $eventType->setDefaultColor($color);
                $eventType->setDefaultNumberingStyle($numberingStyle);
            }

            $this->addReference($reference, $eventType);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 10;
    }
}
