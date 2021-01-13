<?php

namespace eCamp\CoreData;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use eCamp\Core\Entity\ActivityType;
use eCamp\Core\Entity\ActivityTypeContentType;
use eCamp\Core\Entity\ActivityTypeFactory;
use eCamp\Core\Entity\ContentType;

class ActivityTypeData extends AbstractFixture implements DependentFixtureInterface {
    public static $LAGERSPORT = 'LAGERSPORT';
    public static $LAGERAKTIVITAET = 'LAGERAKTIVITAET';

    private ObjectManager $manager;

    public function load(ObjectManager $manager) {
        $this->manager = $manager;

        $repository = $manager->getRepository(ActivityType::class);

        $activityType = $repository->findOneBy(['name' => 'Lagersport']);
        if (null == $activityType) {
            $activityType = new ActivityType();
            $activityType->setName('Lagersport');
            $activityType->setDefaultColor('#4CAF50');
            $activityType->setDefaultNumberingStyle('1');
            $manager->persist($activityType);

            // add allowed content types
            $this->addContentType($activityType, $this->getReference(ContentTypeData::$STORYBOARD));
            $this->addContentType($activityType, $this->getReference(ContentTypeData::$STORYCONTEXT));
            $this->addContentType($activityType, $this->getReference(ContentTypeData::$SAFETYCONCEPT));
            $this->addContentType($activityType, $this->getReference(ContentTypeData::$NOTES));
            $this->addContentType($activityType, $this->getReference(ContentTypeData::$MATERIAL));

            $activityTypeFactory = new ActivityTypeFactory();
            $activityTypeFactory->setName('Wanderung');
            $activityTypeFactory->setFactoryName('');
            $activityType->addActivityTypeFactory($activityTypeFactory);
            $manager->persist($activityTypeFactory);
        }

        $this->addReference(self::$LAGERSPORT, $activityType);

        $activityType = $repository->findOneBy(['name' => 'Lageraktivität']);
        if (null == $activityType) {
            $activityType = new ActivityType();
            $activityType->setName('Lageraktivität');
            $activityType->setDefaultColor('#FF9800');
            $activityType->setDefaultNumberingStyle('A');
            $manager->persist($activityType);

            // add allowed content types
            //$this->addContentType($activityType, $this->getReference(ContentTypeData::$STORYBOARD));
            $this->addContentType($activityType, $this->getReference(ContentTypeData::$STORYCONTEXT));
            $this->addContentType($activityType, $this->getReference(ContentTypeData::$NOTES));
            $this->addContentType($activityType, $this->getReference(ContentTypeData::$MATERIAL));
            $this->addContentType($activityType, $this->getReference(ContentTypeData::$LATHEMATICAREA));

            $activityTypeFactory = new ActivityTypeFactory();
            $activityTypeFactory->setName('TABS');
            $activityTypeFactory->setFactoryName('');
            $activityType->addActivityTypeFactory($activityTypeFactory);
            $manager->persist($activityTypeFactory);
        }
        $this->addReference(self::$LAGERAKTIVITAET, $activityType);

        $manager->flush();
    }

    public function getDependencies() {
        return [ContentTypeData::class];
    }

    private function addContentType(ActivityType $activityType, ContentType $contentType) {
        $activityTypeContentType = new ActivityTypeContentType();
        $activityTypeContentType->setContentType($contentType);
        $activityType->addActivityTypeContentType($activityTypeContentType);
        $this->manager->persist($activityTypeContentType);

        return $activityTypeContentType;
    }
}
