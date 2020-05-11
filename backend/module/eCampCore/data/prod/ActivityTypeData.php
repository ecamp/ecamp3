<?php

namespace eCamp\CoreData;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use eCamp\Core\Entity\ActivityType;
use eCamp\Core\Entity\ActivityTypeContentType;
use eCamp\Core\Entity\ActivityTypeFactory;

class ActivityTypeData extends AbstractFixture implements DependentFixtureInterface {
    public static $LAGERSPORT = 'LAGERSPORT';
    public static $LAGERAKTIVITAET = 'LAGERAKTIVITAET';

    public function load(ObjectManager $manager) {
        $repository = $manager->getRepository(ActivityType::class);

        $activityType = $repository->findOneBy(['name' => 'Lagersport']);
        if (null == $activityType) {
            $activityType = new ActivityType();
            $activityType->setName('Lagersport');
            $activityType->setDefaultColor('#4CAF50');
            $activityType->setDefaultNumberingStyle('1');
            $manager->persist($activityType);

            $contentType = $this->getReference(ContentTypeData::$TEXTAREA);
            $activityTypeContentType = new ActivityTypeContentType();
            $activityTypeContentType->setContentType($contentType);
            $activityTypeContentType->setMinNumberContentTypeInstances(1);
            $activityTypeContentType->setMaxNumberContentTypeInstances(3);
            $activityType->addActivityTypeContentType($activityTypeContentType);
            $manager->persist($activityTypeContentType);

            $contentType = $this->getReference(ContentTypeData::$STORYBOARD);
            $activityTypeContentType = new ActivityTypeContentType();
            $activityTypeContentType->setContentType($contentType);
            $activityTypeContentType->setMinNumberContentTypeInstances(1);
            $activityTypeContentType->setMaxNumberContentTypeInstances(3);
            $activityType->addActivityTypeContentType($activityTypeContentType);
            $manager->persist($activityTypeContentType);

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

            $contentType = $this->getReference(ContentTypeData::$TEXTAREA);
            $activityTypeContentType = new ActivityTypeContentType();
            $activityTypeContentType->setContentType($contentType);
            $activityTypeContentType->setMinNumberContentTypeInstances(1);
            $activityTypeContentType->setMaxNumberContentTypeInstances(100);
            $activityType->addActivityTypeContentType($activityTypeContentType);
            $manager->persist($activityTypeContentType);

            $contentType = $this->getReference(ContentTypeData::$RICHTEXT);
            $activityTypeContentType = new ActivityTypeContentType();
            $activityTypeContentType->setContentType($contentType);
            $activityTypeContentType->setMinNumberContentTypeInstances(3);
            $activityTypeContentType->setMaxNumberContentTypeInstances(3);
            $activityType->addActivityTypeContentType($activityTypeContentType);
            $manager->persist($activityTypeContentType);

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
}
