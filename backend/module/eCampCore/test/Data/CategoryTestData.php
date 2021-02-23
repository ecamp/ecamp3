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

    public function load(ObjectManager $manager): void {
        /** @var Camp $camp */
        $camp = $this->getReference(CampTestData::$CAMP1);

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

        $manager->persist($categoryLS);
        $manager->persist($categoryLA);
        $manager->flush();

        $this->addReference(self::$CATEGORY1, $categoryLS);
        $this->addReference(self::$CATEGORY2, $categoryLA);
    }

    public function getDependencies() {
        return [CampTestData::class];
    }
}
