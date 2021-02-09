<?php

namespace eCamp\CoreData;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\Category;
use eCamp\Core\Entity\CategoryContentType;
use eCamp\Core\Entity\ContentType;

class CategoryData extends AbstractFixture implements DependentFixtureInterface {
    public static $EVENTCATEGORY_1_LS = Category::class.':EVENTCATEGORY_1_LS';
    public static $EVENTCATEGORY_1_LA = Category::class.':EVENTCATEGORY_1_LA';
    public static $EVENTCATEGORY_2_LS = Category::class.':EVENTCATEGORY_2_LS';
    public static $EVENTCATEGORY_2_LA = Category::class.':EVENTCATEGORY_2_LA';

    public function load(ObjectManager $manager) {
        $repository = $manager->getRepository(Category::class);

        /** @var Camp $camp */
        $camp = $this->getReference(CampData::$CAMP_1);

        $category = $repository->findOneBy(['camp' => $camp, 'name' => 'Lagersport']);
        if (null == $category) {
            $category = new Category();
            $category->setCamp($camp);
            $category->setName('Lagersport');
            $category->setShort('LS');
            $category->setColor('#4CAF50');
            $category->setNumberingStyle('1');
            $manager->persist($category);

            // add prefered content types
            $this->addContentType($manager, $category, $this->getReference(ContentTypeData::$STORYBOARD));
            $this->addContentType($manager, $category, $this->getReference(ContentTypeData::$STORYCONTEXT));
            $this->addContentType($manager, $category, $this->getReference(ContentTypeData::$SAFETYCONCEPT));
            $this->addContentType($manager, $category, $this->getReference(ContentTypeData::$NOTES));
            $this->addContentType($manager, $category, $this->getReference(ContentTypeData::$MATERIAL));
        }
        $this->addReference(self::$EVENTCATEGORY_1_LS, $category);

        $category = $repository->findOneBy(['camp' => $camp, 'name' => 'Lageraktivität']);
        if (null == $category) {
            $category = new Category();
            $category->setCamp($camp);
            $category->setName('Lageraktivität');
            $category->setShort('LA');
            $category->setColor('#FF9800');
            $category->setNumberingStyle('A');
            $manager->persist($category);

            // add prefered content types
            $this->addContentType($manager, $category, $this->getReference(ContentTypeData::$STORYCONTEXT));
            $this->addContentType($manager, $category, $this->getReference(ContentTypeData::$NOTES));
            $this->addContentType($manager, $category, $this->getReference(ContentTypeData::$MATERIAL));
            $this->addContentType($manager, $category, $this->getReference(ContentTypeData::$LATHEMATICAREA));
        }
        $this->addReference(self::$EVENTCATEGORY_1_LA, $category);

        /** @var Camp $camp */
        $camp = $this->getReference(CampData::$CAMP_2);

        $category = $repository->findOneBy(['camp' => $camp, 'name' => 'Lagersport']);
        if (null == $category) {
            $category = new Category();
            $category->setCamp($camp);
            $category->setName('Lagersport');
            $category->setShort('LS');
            $category->setColor('#4CAF50');
            $category->setNumberingStyle('1');
            $manager->persist($category);

            // add prefered content types
            $this->addContentType($manager, $category, $this->getReference(ContentTypeData::$STORYBOARD));
            $this->addContentType($manager, $category, $this->getReference(ContentTypeData::$STORYCONTEXT));
            $this->addContentType($manager, $category, $this->getReference(ContentTypeData::$SAFETYCONCEPT));
            $this->addContentType($manager, $category, $this->getReference(ContentTypeData::$NOTES));
            $this->addContentType($manager, $category, $this->getReference(ContentTypeData::$MATERIAL));
        }
        $this->addReference(self::$EVENTCATEGORY_2_LS, $category);

        $category = $repository->findOneBy(['camp' => $camp, 'name' => 'Lageraktivität']);
        if (null == $category) {
            $category = new Category();
            $category->setCamp($camp);
            $category->setName('Lageraktivität');
            $category->setShort('LA');
            $category->setColor('#FF9800');
            $category->setNumberingStyle('A');
            $manager->persist($category);

            // add prefered content types
            $this->addContentType($manager, $category, $this->getReference(ContentTypeData::$STORYCONTEXT));
            $this->addContentType($manager, $category, $this->getReference(ContentTypeData::$NOTES));
            $this->addContentType($manager, $category, $this->getReference(ContentTypeData::$MATERIAL));
            $this->addContentType($manager, $category, $this->getReference(ContentTypeData::$LATHEMATICAREA));
        }
        $this->addReference(self::$EVENTCATEGORY_2_LA, $category);

        $manager->flush();
    }

    public function getDependencies() {
        return [CampData::class, ContentTypeData::class];
    }

    private function addContentType(ObjectManager $manager, Category $category, ContentType $contentType) {
        $categoryContentType = new CategoryContentType();
        $categoryContentType->setContentType($contentType);
        $category->addCategoryContentType($categoryContentType);
        $manager->persist($categoryContentType);

        return $categoryContentType;
    }
}
