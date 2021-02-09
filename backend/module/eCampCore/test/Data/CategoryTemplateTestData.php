<?php

namespace eCamp\CoreTest\Data;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use eCamp\Core\Entity\ActivityCategoryTemplate;
use eCamp\Core\Entity\CategoryTemplate;

class CategoryTemplateTestData extends AbstractFixture implements DependentFixtureInterface {
    public static $TEMPLATE1 = ActivityCategoryTemplate::class.':Template1';

    public function load(ObjectManager $manager) {
        $campTemplate = $this->getReference(CampTemplateTestData::$TEMPLATE1);

        $categoryTemplate = new CategoryTemplate();
        $categoryTemplate->setShort('AC');
        $categoryTemplate->setName('ActivityCategory1');
        $categoryTemplate->setColor('#FF00FF');
        $categoryTemplate->setNumberingStyle('i');
        $campTemplate->addCategoryTemplate($categoryTemplate);

        $manager->persist($categoryTemplate);
        $manager->flush();

        $this->addReference(self::$TEMPLATE1, $categoryTemplate);
    }

    public function getDependencies() {
        return [CampTemplateTestData::class];
    }
}
