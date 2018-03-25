<?php

namespace eCamp\CoreData;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use eCamp\Core\Entity\Event;
use eCamp\Core\Entity\EventInstance;
use eCamp\Core\Entity\Period;

class EventInstanceData extends AbstractFixture implements DependentFixtureInterface
{
    public static $EVENT_INSTANCE_1_LS_1 = Event::class . ':EVENT_INSTANCE_1_LS_1';
    public static $EVENT_INSTANCE_1_LS_2 = Event::class . ':EVENT_INSTANCE_1_LS_2';
    public static $EVENT_INSTANCE_1_LA_1 = Event::class . ':EVENT_INSTANCE_1_LA_1';
    public static $EVENT_INSTANCE_1_LA_2 = Event::class . ':EVENT_INSTANCE_1_LA_2';
    public static $EVENT_INSTANCE_2_LS = Event::class . ':EVENT_INSTANCE_2_LS';
    public static $EVENT_INSTANCE_2_LA = Event::class . ':EVENT_INSTANCE_2_LA';

    public function load(ObjectManager $manager) {
        $repository = $manager->getRepository(EventInstance::class);


        /** @var Period $period */
        $period = $this->getReference(PeriodData::$PERIOD_1);

        /** @var Event $event */
        $event = $this->getReference(EventData::$EVENT_1_LS);

        $eventInstances = $repository->findBy(['period' => $period, 'event' => $event]);
        $eventInstance = array_shift($eventInstances);
        if ($eventInstance == null) {
            $eventInstance = new EventInstance();
            $eventInstance->setPeriod($period);
            $eventInstance->setEvent($event);
            $eventInstance->setStart(600);
            $eventInstance->setLength(120);
            $eventInstance->setLeft(0);
            $eventInstance->setWidth(1);

            $manager->persist($eventInstance);
        }
        $this->addReference(self::$EVENT_INSTANCE_1_LS_1, $eventInstance);

        $eventInstance = array_shift($eventInstances);
        if ($eventInstance == null) {
            $eventInstance = new EventInstance();
            $eventInstance->setPeriod($period);
            $eventInstance->setEvent($event);
            $eventInstance->setStart(2040);
            $eventInstance->setLength(180);
            $eventInstance->setLeft(0);
            $eventInstance->setWidth(1);

            $manager->persist($eventInstance);
        }
        $this->addReference(self::$EVENT_INSTANCE_1_LS_2, $eventInstance);


        /** @var Event $event */
        $event = $this->getReference(EventData::$EVENT_1_LA);

        $eventInstances = $repository->findBy(['period' => $period, 'event' => $event]);
        $eventInstance = array_shift($eventInstances);
        if ($eventInstance == null) {
            $eventInstance = new EventInstance();
            $eventInstance->setPeriod($period);
            $eventInstance->setEvent($event);
            $eventInstance->setStart(900);
            $eventInstance->setLength(150);
            $eventInstance->setLeft(0);
            $eventInstance->setWidth(1);

            $manager->persist($eventInstance);
        }
        $this->addReference(self::$EVENT_INSTANCE_1_LA_1, $eventInstance);

        $eventInstance = array_shift($eventInstances);
        if ($eventInstance == null) {
            $eventInstance = new EventInstance();
            $eventInstance->setPeriod($period);
            $eventInstance->setEvent($event);
            $eventInstance->setStart(2340);
            $eventInstance->setLength(120);
            $eventInstance->setLeft(0);
            $eventInstance->setWidth(1);

            $manager->persist($eventInstance);
        }
        $this->addReference(self::$EVENT_INSTANCE_1_LA_2, $eventInstance);




        /** @var Period $period */
        $period = $this->getReference(PeriodData::$PERIOD_2);

        /** @var Event $event */
        $event = $this->getReference(EventData::$EVENT_2_LS);

        $eventInstances = $repository->findBy(['period' => $period, 'event' => $event]);
        $eventInstance = array_shift($eventInstances);
        if ($eventInstance == null) {
            $eventInstance = new EventInstance();
            $eventInstance->setPeriod($period);
            $eventInstance->setEvent($event);
            $eventInstance->setStart(600);
            $eventInstance->setLength(120);
            $eventInstance->setLeft(0);
            $eventInstance->setWidth(1);

            $manager->persist($eventInstance);
        }
        $this->addReference(self::$EVENT_INSTANCE_2_LS, $eventInstance);


        /** @var Event $event */
        $event = $this->getReference(EventData::$EVENT_2_LA);

        $eventInstances = $repository->findBy(['period' => $period, 'event' => $event]);
        $eventInstance = array_shift($eventInstances);
        if ($eventInstance == null) {
            $eventInstance = new EventInstance();
            $eventInstance->setPeriod($period);
            $eventInstance->setEvent($event);
            $eventInstance->setStart(900);
            $eventInstance->setLength(90);
            $eventInstance->setLeft(0);
            $eventInstance->setWidth(1);

            $manager->persist($eventInstance);
        }
        $this->addReference(self::$EVENT_INSTANCE_2_LA, $eventInstance);



        $manager->flush();
    }

    function getDependencies() {
        return [ PeriodData::class, EventData::class ];
    }
}
