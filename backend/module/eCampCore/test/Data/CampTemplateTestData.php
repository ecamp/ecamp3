<?php

namespace eCamp\CoreTest\Data;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;
use eCamp\Core\Entity\CampTemplate;

class CampTemplateTestData extends AbstractFixture {
    public static $TEMPLATE1 = CampTemplate::class.':Template1';

    public function load(ObjectManager $manager): void {
        $campTemplate = new CampTemplate();
        $campTemplate->setName('CampTemplate1');

        $manager->persist($campTemplate);
        $manager->flush();

        $this->addReference(self::$TEMPLATE1, $campTemplate);
    }
}
