<?php

namespace eCamp\CoreTest\Data;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;
use eCamp\Core\Entity\CampTemplate;

class CampTemplateTestData extends AbstractFixture {
    public static $TYPE1 = CampTemplate::class.':Type1';

    public function load(ObjectManager $manager) {
        $campTemplate = new CampTemplate();
        $campTemplate->setName('CampTemplate1');

        $manager->persist($campTemplate);
        $manager->flush();

        $this->addReference(self::$TYPE1, $campTemplate);
    }
}
