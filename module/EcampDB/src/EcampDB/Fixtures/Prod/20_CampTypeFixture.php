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
    const AUSBILDUNG_WOLF_BASIS = 'camptype-wolf-basiskurs';
    const AUSBILDUNG_PANO       = 'camptype-panokurs';
    const AUSBILDUNG_EXPERTE    = 'camptype-experte';

    public function load(ObjectManager $manager)
    {
        $this->load_($manager, array(
            array(
                'name' => 'J+S Lager',
                'isCourse' => false,
                'organization' => null,
                'isJS' => true,
                'eventTypes' => array(
                    EventTypeFixture::LAGERSPORT,
                    EventTypeFixture::LAGERAKTIVITAET,
                    EventTypeFixture::LAGERPROGRAMM
                ),
                'reference' => self::KINDERSPORT
            ),
            array(
                'name' => 'Lager ohne J+S',
                'isCourse' => false,
                'organization' => null,
                'isJS' => false,
                'eventTypes' => array(
                    EventTypeFixture::LAGERSPORT,
                    EventTypeFixture::LAGERAKTIVITAET,
                    EventTypeFixture::LAGERPROGRAMM
                ),
                'reference' => self::JUGENDSPORT
            ),
            array(
                'name' => 'Wolfstufe Basiskurs',
                'isCourse' => true,
                'organization' => CampType::ORGANIZATION_PBS,
                'isJS' => true,
                'eventTypes' => array(
                    EventTypeFixture::AUSBILDUNG_PBS_JS
                ),
                'reference' => self::AUSBILDUNG_WOLF_BASIS
            ),
            array(
                'name' => 'Panokurs',
                'isCourse' => true,
                'organization' => CampType::ORGANIZATION_PBS,
                'isJS' => false,
                'eventTypes' => array(
                    EventTypeFixture::AUSBILDUNG
                ),
                'reference' => self::AUSBILDUNG_PANO
            ),
            array(
                'name' => 'Expertenkurs',
                'isCourse' => true,
                'organization' => CampType::ORGANIZATION_JS,
                'isJS' => true,
                'eventTypes' => array(
                    EventTypeFixture::AUSBILDUNG
                ),
                'reference' => self::AUSBILDUNG_EXPERTE
            )
        ));
    }

    public function load_(ObjectManager $manager, array $config)
    {
        $campTypeRepo = $manager->getRepository('EcampCore\Entity\CampType');

        foreach ($config as $campTypeConfig) {
            $name = $campTypeConfig['name'];
            $isCourse = $campTypeConfig['isCourse'];
            $organization = $campTypeConfig['organization'];
            $isJS = $campTypeConfig['isJS'];
            $eventTypes = $campTypeConfig['eventTypes'];
            $reference = $campTypeConfig['reference'];

            /** @var CampType $campType */
            $campType = $campTypeRepo->findOneBy(array('name' => $name));

            if ($campType == null) {
                $campType = new CampType($name, $isCourse, $organization, $isJS);
                $manager->persist($campType);
            } else {

            }

            foreach ($eventTypes as $eventType) {
                /** @var EventType $eventType */
                $eventType = $this->getReference($eventType);
                if (!$eventType->getCampTypes()->contains($campType)) {
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
