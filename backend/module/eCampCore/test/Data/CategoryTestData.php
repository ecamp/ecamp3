<?php

namespace eCamp\CoreTest\Data;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\Category;

class CategoryTestData extends AbstractFixture implements DependentFixtureInterface {
    public static $CATEGORY1 = Category::class.':CATEGORY1';
    public static $CATEGORY2 = Category::class.':CATEGORY2';
    public static $CATEGORY1_CAMP2 = Category::class.':CATEGORY1_CAMP2';

    public function load(ObjectManager $manager): void {
        /** @var Camp $camp */
        $camp = $this->getReference(CampTestData::$CAMP1);
        /** @var Camp $camp */
        $camp2 = $this->getReference(CampTestData::$CAMP2);

        $categoryLS = new Category();
        $categoryLS->setCamp($camp);
        $categoryLS->setName('ActivityCategory1');
        $categoryLS->setShort('LS');
        $categoryLS->setColor('#FF9800');
        $categoryLS->setNumberingStyle('i');

        $categoryLA = new Category();
        $categoryLA->setCamp($camp);
        $categoryLA->setName('ActivityCategory2');
        $categoryLA->setShort('LA');
        $categoryLA->setColor('#4CAF50');
        $categoryLA->setNumberingStyle('i');

        $categoryLSCamp2 = new Category();
        $categoryLSCamp2->setCamp($camp2);
        $categoryLSCamp2->setName('Category1 Camp2');
        $categoryLSCamp2->setShort('LS');
        $categoryLSCamp2->setColor('#FF9800');
        $categoryLSCamp2->setNumberingStyle('i');

        $manager->persist($categoryLS);
        $manager->persist($categoryLA);
        $manager->persist($categoryLSCamp2);
        $manager->flush();

        $this->addReference(self::$CATEGORY1, $categoryLS);
        $this->addReference(self::$CATEGORY2, $categoryLA);
        $this->addReference(self::$CATEGORY1_CAMP2, $categoryLSCamp2);
    }

    public function getDependencies() {
        return [CampTestData::class];
    }
}
