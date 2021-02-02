<?php

namespace eCamp\CoreData;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use eCamp\Core\Entity\ActivityCategory;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\ContentType;
use eCamp\Core\Entity\ContentTypeConfig;

class ActivityCategoryData extends AbstractFixture implements DependentFixtureInterface {
    public static $EVENTCATEGORY_1_LS = ActivityCategory::class.':EVENTCATEGORY_1_LS';
    public static $EVENTCATEGORY_1_LA = ActivityCategory::class.':EVENTCATEGORY_1_LA';
    public static $EVENTCATEGORY_2_LS = ActivityCategory::class.':EVENTCATEGORY_2_LS';
    public static $EVENTCATEGORY_2_LA = ActivityCategory::class.':EVENTCATEGORY_2_LA';

    public function load(ObjectManager $manager) {
        $repository = $manager->getRepository(ActivityCategory::class);

        /** @var Camp $camp */
        $camp = $this->getReference(CampData::$CAMP_1);

        $activityCategory = $repository->findOneBy(['camp' => $camp, 'name' => 'Lagersport']);
        if (null == $activityCategory) {
            $activityCategory = new ActivityCategory();
            $activityCategory->setCamp($camp);
            $activityCategory->setName('Lagersport');
            $activityCategory->setShort('LS');
            $activityCategory->setColor('#4CAF50');
            $activityCategory->setNumberingStyle('1');
            $manager->persist($activityCategory);

            // add prefered content types
            $this->addContentType($manager, $activityCategory, $this->getReference(ContentTypeData::$STORYBOARD));
            $this->addContentType($manager, $activityCategory, $this->getReference(ContentTypeData::$STORYCONTEXT));
            $this->addContentType($manager, $activityCategory, $this->getReference(ContentTypeData::$SAFETYCONCEPT));
            $this->addContentType($manager, $activityCategory, $this->getReference(ContentTypeData::$NOTES));
            $this->addContentType($manager, $activityCategory, $this->getReference(ContentTypeData::$MATERIAL));
        }
        $this->addReference(self::$EVENTCATEGORY_1_LS, $activityCategory);

        $activityCategory = $repository->findOneBy(['camp' => $camp, 'name' => 'Lageraktivität']);
        if (null == $activityCategory) {
            $activityCategory = new ActivityCategory();
            $activityCategory->setCamp($camp);
            $activityCategory->setName('Lageraktivität');
            $activityCategory->setShort('LA');
            $activityCategory->setColor('#FF9800');
            $activityCategory->setNumberingStyle('A');
            $manager->persist($activityCategory);

            // add prefered content types
            $this->addContentType($manager, $activityCategory, $this->getReference(ContentTypeData::$STORYCONTEXT));
            $this->addContentType($manager, $activityCategory, $this->getReference(ContentTypeData::$NOTES));
            $this->addContentType($manager, $activityCategory, $this->getReference(ContentTypeData::$MATERIAL));
            $this->addContentType($manager, $activityCategory, $this->getReference(ContentTypeData::$LATHEMATICAREA));
        }
        $this->addReference(self::$EVENTCATEGORY_1_LA, $activityCategory);

        /** @var Camp $camp */
        $camp = $this->getReference(CampData::$CAMP_2);

        $activityCategory = $repository->findOneBy(['camp' => $camp, 'name' => 'Lagersport']);
        if (null == $activityCategory) {
            $activityCategory = new ActivityCategory();
            $activityCategory->setCamp($camp);
            $activityCategory->setName('Lagersport');
            $activityCategory->setShort('LS');
            $activityCategory->setColor('#4CAF50');
            $activityCategory->setNumberingStyle('1');
            $manager->persist($activityCategory);

            // add prefered content types
            $this->addContentType($manager, $activityCategory, $this->getReference(ContentTypeData::$STORYBOARD));
            $this->addContentType($manager, $activityCategory, $this->getReference(ContentTypeData::$STORYCONTEXT));
            $this->addContentType($manager, $activityCategory, $this->getReference(ContentTypeData::$SAFETYCONCEPT));
            $this->addContentType($manager, $activityCategory, $this->getReference(ContentTypeData::$NOTES));
            $this->addContentType($manager, $activityCategory, $this->getReference(ContentTypeData::$MATERIAL));
        }
        $this->addReference(self::$EVENTCATEGORY_2_LS, $activityCategory);

        $activityCategory = $repository->findOneBy(['camp' => $camp, 'name' => 'Lageraktivität']);
        if (null == $activityCategory) {
            $activityCategory = new ActivityCategory();
            $activityCategory->setCamp($camp);
            $activityCategory->setName('Lageraktivität');
            $activityCategory->setShort('LA');
            $activityCategory->setColor('#FF9800');
            $activityCategory->setNumberingStyle('A');
            $manager->persist($activityCategory);

            // add prefered content types
            $this->addContentType($manager, $activityCategory, $this->getReference(ContentTypeData::$STORYCONTEXT));
            $this->addContentType($manager, $activityCategory, $this->getReference(ContentTypeData::$NOTES));
            $this->addContentType($manager, $activityCategory, $this->getReference(ContentTypeData::$MATERIAL));
            $this->addContentType($manager, $activityCategory, $this->getReference(ContentTypeData::$LATHEMATICAREA));
        }
        $this->addReference(self::$EVENTCATEGORY_2_LA, $activityCategory);

        $manager->flush();
    }

    public function getDependencies() {
        return [CampData::class, ContentTypeData::class];
    }

    private function addContentType(ObjectManager $manager, ActivityCategory $activityCategory, ContentType $contentType) {
        $contentTypeConfig = new ContentTypeConfig();
        $contentTypeConfig->setContentType($contentType);
        $activityCategory->addContentTypeConfig($contentTypeConfig);
        $manager->persist($contentTypeConfig);

        return $contentTypeConfig;
    }
}
