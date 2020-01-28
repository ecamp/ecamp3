<?php

namespace eCamp\CoreData;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\EventCategory;
use eCamp\Core\Entity\EventType;

class EventCategoryData extends AbstractFixture implements DependentFixtureInterface {
    public static $EVENTCATEGORY_1_LS = EventCategory::class . ':EVENTCATEGORY_1_LS';
    public static $EVENTCATEGORY_1_LA = EventCategory::class . ':EVENTCATEGORY_1_LA';
    public static $EVENTCATEGORY_2_LS = EventCategory::class . ':EVENTCATEGORY_2_LS';
    public static $EVENTCATEGORY_2_LA = EventCategory::class . ':EVENTCATEGORY_2_LA';

    public function load(ObjectManager $manager) {
        $repository = $manager->getRepository(EventCategory::class);

        /** @var Camp $camp */
        $camp = $this->getReference(CampData::$CAMP_1);

        /** @var EventType $eventType */
        $eventType = $this->getReference(EventTypeData::$LAGERSPORT);
        $eventCategory = $repository->findOneBy(['camp' => $camp, 'name' => 'Lagersport']);
        if ($eventCategory == null) {
            $eventCategory = new EventCategory();
            $eventCategory->setCamp($camp);
            $eventCategory->setEventType($eventType);
            $eventCategory->setName('Lagersport');
            $eventCategory->setShort('LS');
            $eventCategory->setColor('#4CAF50');

            $manager->persist($eventCategory);
        }
        $this->addReference(self::$EVENTCATEGORY_1_LS, $eventCategory);

        /** @var EventType $eventType */
        $eventType = $this->getReference(EventTypeData::$LAGERSPORT);
        $eventCategory = $repository->findOneBy(['camp' => $camp, 'name' => 'Lageraktivität']);
        if ($eventCategory == null) {
            $eventCategory = new EventCategory();
            $eventCategory->setCamp($camp);
            $eventCategory->setEventType($eventType);
            $eventCategory->setName('Lageraktivität');
            $eventCategory->setShort('LA');
            $eventCategory->setColor('#FF9800');

            $manager->persist($eventCategory);
        }
        $this->addReference(self::$EVENTCATEGORY_1_LA, $eventCategory);


        /** @var Camp $camp */
        $camp = $this->getReference(CampData::$CAMP_2);

        /** @var EventType $eventType */
        $eventType = $this->getReference(EventTypeData::$LAGERSPORT);
        $eventCategory = $repository->findOneBy(['camp' => $camp, 'name' => 'Lagersport']);
        if ($eventCategory == null) {
            $eventCategory = new EventCategory();
            $eventCategory->setCamp($camp);
            $eventCategory->setEventType($eventType);
            $eventCategory->setName('Lagersport');
            $eventCategory->setShort('LS');
            $eventCategory->setColor('#4CAF50');

            $manager->persist($eventCategory);
        }
        $this->addReference(self::$EVENTCATEGORY_2_LS, $eventCategory);

        /** @var EventType $eventType */
        $eventType = $this->getReference(EventTypeData::$LAGERSPORT);
        $eventCategory = $repository->findOneBy(['camp' => $camp, 'name' => 'Lageraktivität']);
        if ($eventCategory == null) {
            $eventCategory = new EventCategory();
            $eventCategory->setCamp($camp);
            $eventCategory->setEventType($eventType);
            $eventCategory->setName('Lageraktivität');
            $eventCategory->setShort('LA');
            $eventCategory->setColor('#FF9800');

            $manager->persist($eventCategory);
        }
        $this->addReference(self::$EVENTCATEGORY_2_LA, $eventCategory);


        $manager->flush();
    }

    public function getDependencies() {
        return [ CampData::class, EventTypeData::class ];
    }
}
