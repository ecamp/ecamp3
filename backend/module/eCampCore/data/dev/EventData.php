<?php

namespace eCamp\CoreData;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\Event;
use eCamp\Core\Entity\EventCategory;
use eCamp\Core\Entity\EventPlugin;

class EventData extends AbstractFixture implements DependentFixtureInterface {
    public static $EVENT_1_LS = Event::class . ':EVENT_1_LS';
    public static $EVENT_1_LA = Event::class . ':EVENT_1_LA';
    public static $EVENT_2_LS = Event::class . ':EVENT_2_LS';
    public static $EVENT_2_LA = Event::class . ':EVENT_2_LA';

    public function load(ObjectManager $manager) {
        $repository = $manager->getRepository(Event::class);

        /** @var Camp $camp */
        $camp = $this->getReference(CampData::$CAMP_1);
        /** @var EventCategory $eventCategoryLs */
        $eventCategoryLs = $this->getReference(EventCategoryData::$EVENTCATEGORY_1_LS);
        /** @var EventCategory $eventCategoryLa */
        $eventCategoryLa = $this->getReference(EventCategoryData::$EVENTCATEGORY_1_LA);

        $event = $repository->findOneBy(['camp' => $camp, 'title' => 'Event LS']);
        if ($event == null) {
            $event = new Event();
            $event->setCamp($camp);
            $event->setTitle('Event LS');
            $event->setEventCategory($eventCategoryLs);

            $manager->persist($event);
        }
        $this->addReference(self::$EVENT_1_LS, $event);

        $event = $repository->findOneBy(['camp' => $camp, 'title' => 'Event LA']);
        if ($event == null) {
            $event = new Event();
            $event->setCamp($camp);
            $event->setTitle('Event LA');
            $event->setEventCategory($eventCategoryLa);

            $manager->persist($event);
        }
        $this->addReference(self::$EVENT_1_LA, $event);


        /** @var Camp $camp */
        $camp = $this->getReference(CampData::$CAMP_2);
        /** @var EventCategory $eventCategoryLs */
        $eventCategoryLs = $this->getReference(EventCategoryData::$EVENTCATEGORY_2_LS);
        /** @var EventCategory $eventCategoryLa */
        $eventCategoryLa = $this->getReference(EventCategoryData::$EVENTCATEGORY_2_LA);

        $event = $repository->findOneBy(['camp' => $camp, 'title' => 'Event LS']);
        if ($event == null) {
            $event = new Event();
            $event->setCamp($camp);
            $event->setTitle('Event LS');
            $event->setEventCategory($eventCategoryLs);

            $manager->persist($event);
        }
        $this->addReference(self::$EVENT_2_LS, $event);

        $event = $repository->findOneBy(['camp' => $camp, 'title' => 'Event LA']);
        if ($event == null) {
            $event = new Event();
            $event->setCamp($camp);
            $event->setTitle('Event LA');
            $event->setEventCategory($eventCategoryLa);

            $manager->persist($event);
        }
        $this->addReference(self::$EVENT_2_LA, $event);

        $manager->flush();
    }

    public function getDependencies() {
        return [ CampData::class, EventCategoryData::class ];
    }
}
