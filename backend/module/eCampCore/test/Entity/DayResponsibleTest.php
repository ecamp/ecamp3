<?php

namespace eCamp\CoreTest\Entity;

use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\CampCollaboration;
use eCamp\Core\Entity\Day;
use eCamp\Core\Entity\DayResponsible;
use eCamp\Core\Entity\Period;
use eCamp\Core\Entity\User;
use eCamp\LibTest\PHPUnit\AbstractTestCase;

/**
 * @internal
 */
class DayResponsibleTest extends AbstractTestCase {
    public function testDayResponsible(): void {
        $user = new User();

        $camp = new Camp();

        $period = new Period();
        $period->setCamp($camp);

        $day = new Day();
        $day->setPeriod($period);
        $day->setDayOffset(0);

        $campCollaboration = new CampCollaboration();
        $campCollaboration->setCamp($camp);
        $campCollaboration->setUser($user);

        $dayResponsible = new DayResponsible();
        $dayResponsible->setDay($day);
        $dayResponsible->setCampCollaboration($campCollaboration);

        $this->assertEquals($camp, $dayResponsible->getCamp());
        $this->assertEquals($day, $dayResponsible->getDay());
        $this->assertEquals($campCollaboration, $dayResponsible->getCampCollaboration());
    }
}
