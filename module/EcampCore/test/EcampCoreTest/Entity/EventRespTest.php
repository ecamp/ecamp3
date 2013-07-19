<?php

namespace EcampCoreTest\Entity;

use EcampCore\Entity\CampType;
use EcampCore\Entity\EventType;
use EcampCore\Entity\EventPrototype;
use EcampCore\Entity\Camp;
use EcampCore\Entity\Event;
use EcampCore\Entity\User;
use EcampCore\Entity\EventResp;
use EcampCore\Entity\CampCollaboration;

class EventRespTest extends \PHPUnit_Framework_TestCase
{

    public function testEventResp()
    {
        $campType = new CampType('name', 'type');
        $eventType = new EventType($campType);
        $eventPrototype = new EventPrototype('any event prototype');
        $eventType->getEventPrototypes()->add($eventPrototype);

        $user = new User();

        $camp = new Camp($campType);
        $event = new Event($camp, $eventPrototype);
        $event->setTitle('any title');

        $campCollaboration = CampCollaboration::createRequest($user, $camp);
        $campCollaboration->acceptRequest($user);

        $eventResp = new EventResp($event, $campCollaboration);

        $this->assertEquals($camp, $eventResp->getCamp());
        $this->assertEquals($event, $eventResp->getEvent());
        $this->assertEquals($user, $eventResp->getUser());
        $this->assertEquals($campCollaboration, $eventResp->getCampCollaboration());

        $this->assertContains($eventResp, $event->getEventResps());
        $this->assertContains($eventResp, $campCollaboration->getEventResps());

        $eventResp->preRemove();

        $this->assertNotContains($eventResp, $event->getEventResps());
        $this->assertNotContains($eventResp, $campCollaboration->getEventResps());
    }

    /**
     * @expectedException OutOfRangeException
     */
    public function testCampMix()
    {
        $campType = new CampType('name', 'type');
        $eventType = new EventType($campType);
        $eventPrototype = new EventPrototype('any event prototype');
        $eventType->getEventPrototypes()->add($eventPrototype);

        $user = new User();

        $campA = new Camp($campType);
        $campB = new Camp($campType);
        $event = new Event($campA, $eventPrototype);

        $campCollaboration = CampCollaboration::createRequest($user, $campB);
        $campCollaboration->acceptRequest($user);

        new EventResp($event, $campCollaboration);
    }

}
