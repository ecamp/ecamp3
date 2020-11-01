<?php

namespace eCamp\CoreTest\Data;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use eCamp\Core\Entity\CampType;

class CampTypeTestData extends AbstractFixture implements DependentFixtureInterface {
    public static $TYPE1 = CampType::class.':Type1';

    public function load(ObjectManager $manager) {
        $org1 = $this->getReference(OrganizationTestData::$ORG1);

        $campType = new CampType();
        $campType->setName('CampType1');
        $campType->setOrganization($org1);
        $campType->setIsJS(false);
        $campType->setIsCourse(false);

        $manager->persist($campType);
        $manager->flush();

        $this->addReference(self::$TYPE1, $campType);
    }

    public function getDependencies() {
        return [OrganizationTestData::class];
    }
}
