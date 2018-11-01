<?php

namespace eCamp\CoreData;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use eCamp\Core\Entity\CampType;
use eCamp\Core\Entity\Organization;

class CampTypeData extends AbstractFixture implements DependentFixtureInterface {
    public static $PBS_JS_KIDS = CampType::class . ':PBS_JS_KIDS';
    public static $PBS_JS_TEEN = CampType::class . ':PBS_JS_TEEN';

    public function load(ObjectManager $manager) {
        $repository = $manager->getRepository(CampType::class);

        $lagersport = $this->getReference(EventTypeData::$LAGERSPORT);
        $lageraktivitaet = $this->getReference(EventTypeData::$LAGERAKTIVITAET);

        $pbs = $this->getReference(OrganizationData::$PBS);


        $campType = $repository->findOneBy(['name' => 'J+S Kids', 'organization' => $pbs]);
        if ($campType == null) {
            $campType = new CampType();
            $campType->setName('J+S Kids');
            $campType->setOrganization($pbs);
            $campType->setIsJS(true);
            $campType->setIsCourse(false);
            $campType->setJsonConfig(json_encode([
                CampType::CNF_EVENT_CATEGORIES => [
                    [
                        'event_type_id' => $lagersport->getId(),
                        'short' => 'LS',
                        'name' => 'Lagersport'
                    ],
                    [
                        'event_type_id' => $lageraktivitaet->getId(),
                        'short' => 'LA',
                        'name' => 'Lageraktivität'
                    ]
                ]
            ]));

            $manager->persist($campType);
        }
        if (!$campType->getEventTypes()->contains($lagersport)) {
            $campType->addEventType($lagersport);
        }
        if (!$campType->getEventTypes()->contains($lageraktivitaet)) {
            $campType->addEventType($lageraktivitaet);
        }
        $this->addReference(self::$PBS_JS_KIDS, $campType);

        $campType = $repository->findOneBy(['name' => 'J+S Teen', 'organization' => $pbs]);
        if ($campType == null) {
            $campType = new CampType();
            $campType->setName('J+S Teen');
            $campType->setOrganization($pbs);
            $campType->setIsJS(true);
            $campType->setIsCourse(false);
            $campType->setJsonConfig(json_encode([
                CampType::CNF_EVENT_CATEGORIES => [
                    [
                        'event_type_id' => $lagersport->getId(),
                        'short' => 'LS',
                        'name' => 'Lagersport'
                    ],
                    [
                        'event_type_id' => $lageraktivitaet->getId(),
                        'short' => 'LA',
                        'name' => 'Lageraktivität'
                    ]
                ]
            ]));

            $manager->persist($campType);
        }
        if (!$campType->getEventTypes()->contains($lagersport)) {
            $campType->addEventType($lagersport);
        }
        if (!$campType->getEventTypes()->contains($lageraktivitaet)) {
            $campType->addEventType($lageraktivitaet);
        }
        $this->addReference(self::$PBS_JS_TEEN, $campType);


        $manager->flush();
    }

    public function getDependencies() {
        return [ OrganizationData::class, EventTypeData::class ];
    }
}
