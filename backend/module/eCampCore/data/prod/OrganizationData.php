<?php

namespace eCamp\CoreData;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;
use eCamp\Core\Entity\Organization;

class OrganizationData extends AbstractFixture {
    public static $JS = Organization::class.':JS';
    public static $PBS = Organization::class.':PBS';

    public function load(ObjectManager $manager) {
        $repository = $manager->getRepository(Organization::class);

        $organization = $repository->findOneBy(['name' => 'JS']);
        if (null == $organization) {
            $organization = new Organization();
            $organization->setName('JS');
            $manager->persist($organization);
        }
        $this->addReference(self::$JS, $organization);

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
