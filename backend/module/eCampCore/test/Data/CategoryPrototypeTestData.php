<?php

namespace eCamp\CoreTest\Data;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\Category;

class CategoryPrototypeTestData extends AbstractFixture implements DependentFixtureInterface {
    public static $PROTOTYPE1 = Category::class.':Prototype1';

    public function load(ObjectManager $manager): void {
        /** @var Camp $camp */
        $camp = $this->getReference(CampPrototypeTestData::$PROTOTYPE1);

        $category = new Category();
        $category->setShort('AC');
        $category->setName('ActivityCategory1');
        $category->setColor('#FF00FF');
        $category->setNumberingStyle('i');
        $camp->addCategory($category);

        $manager->persist($category);
        $manager->flush();

        $this->addReference(self::$PROTOTYPE1, $category);
    }

    public function getDependencies() {
        return [CampPrototypeTestData::class];
    }
}
