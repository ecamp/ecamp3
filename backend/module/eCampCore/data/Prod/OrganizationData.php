<?php

namespace eCamp\CoreData\Prod;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;
use eCamp\Core\Entity\Organization;

class OrganizationData extends AbstractFixture {
    public static $PBS = Organization::class.':PBS';

    public function load(ObjectManager $manager): void {
        $repository = $manager->getRepository(Organization::class);

        $organization = $repository->findOneBy(['name' => 'PBS']);
        if (null == $organization) {
            $organization = new Organization();
            $organization->setName('PBS');
            $manager->persist($organization);
        }
        $this->addReference(self::$PBS, $organization);

        $manager->flush();
    }
}
