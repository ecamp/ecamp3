<?php

namespace eCamp\CoreData;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use eCamp\Core\Entity\CampType;

class CampTypeData extends AbstractFixture implements DependentFixtureInterface {
    public static $JS_KIDS = CampType::class.':JS_KIDS';
    public static $PBS_JS_KIDS = CampType::class.':PBS_JS_KIDS';
    public static $PBS_JS_TEEN = CampType::class.':PBS_JS_TEEN';

    public function load(ObjectManager $manager) {
        $repository = $manager->getRepository(CampType::class);

        $lagersport = $this->getReference(ActivityTypeData::$LAGERSPORT);
        $lageraktivitaet = $this->getReference(ActivityTypeData::$LAGERAKTIVITAET);

        $js = $this->getReference(OrganizationData::$JS);
        $pbs = $this->getReference(OrganizationData::$PBS);

        $campType = $repository->findOneBy(['name' => 'J+S Kids', 'organization' => $js]);
        if (null == $campType) {
            $campType = new CampType();
            $campType->setName('J+S Kids');
            $campType->setOrganization($js);
            $campType->setIsJS(true);
            $campType->setIsCourse(false);
            $campType->setJsonConfig($this->getJsonConfig());

            $manager->persist($campType);
        }
        if (!$campType->getActivityTypes()->contains($lagersport)) {
            $campType->addActivityType($lagersport);
        }
        if (!$campType->getActivityTypes()->contains($lageraktivitaet)) {
            $campType->addActivityType($lageraktivitaet);
        }
        $this->addReference(self::$JS_KIDS, $campType);

        $campType = $repository->findOneBy(['name' => 'J+S Kids', 'organization' => $pbs]);
        if (null == $campType) {
            $campType = new CampType();
            $campType->setName('J+S Kids');
            $campType->setOrganization($pbs);
            $campType->setIsJS(true);
            $campType->setIsCourse(false);
            $campType->setJsonConfig($this->getJsonConfig());

            $manager->persist($campType);
        }
        if (!$campType->getActivityTypes()->contains($lagersport)) {
            $campType->addActivityType($lagersport);
        }
        if (!$campType->getActivityTypes()->contains($lageraktivitaet)) {
            $campType->addActivityType($lageraktivitaet);
        }
        $this->addReference(self::$PBS_JS_KIDS, $campType);

        $campType = $repository->findOneBy(['name' => 'J+S Teen', 'organization' => $pbs]);
        if (null == $campType) {
            $campType = new CampType();
            $campType->setName('J+S Teen');
            $campType->setOrganization($pbs);
            $campType->setIsJS(true);
            $campType->setIsCourse(false);
            $campType->setJsonConfig($this->getJsonConfig());

            $manager->persist($campType);
        }
        if (!$campType->getActivityTypes()->contains($lagersport)) {
            $campType->addActivityType($lagersport);
        }
        if (!$campType->getActivityTypes()->contains($lageraktivitaet)) {
            $campType->addActivityType($lageraktivitaet);
        }
        $this->addReference(self::$PBS_JS_TEEN, $campType);

        $manager->flush();
    }

    public function getDependencies() {
        return [OrganizationData::class, ActivityTypeData::class];
    }

    private function getJsonConfig() {
        $lagersport = $this->getReference(ActivityTypeData::$LAGERSPORT);
        $lageraktivitaet = $this->getReference(ActivityTypeData::$LAGERAKTIVITAET);

        return json_encode([
            CampType::CNF_ACTIVITY_CATEGORIES => [
                [
                    'activityTypeId' => $lagersport->getId(),
                    'short' => 'LS',
                    'name' => $lagersport->getName(),
                    'color' => $lagersport->getDefaultColor(),
                    'numberingStyle' => $lagersport->getDefaultNumberingStyle(),
                ], [
                    'activityTypeId' => $lageraktivitaet->getId(),
                    'short' => 'LA',
                    'name' => $lageraktivitaet->getName(),
                    'color' => $lageraktivitaet->getDefaultColor(),
                    'numberingStyle' => $lageraktivitaet->getDefaultNumberingStyle(),
                ],
            ],
        ]);
    }
}
