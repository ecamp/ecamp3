<?php

namespace eCamp\CoreTest\Data;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;
use eCamp\Core\Entity\Organization;

class OrganizationTestData extends AbstractFixture {
    public static $ORG1 = Organization::class.':Org1';

    public function load(ObjectManager $manager): void {
        $repository = $manager->getRepository(Organization::class);

        $organization = new Organization();
        $organization->setName('Organization1');

        $manager->persist($organization);
        $manager->flush();

        $this->addReference(self::$ORG1, $organization);
    }
}
