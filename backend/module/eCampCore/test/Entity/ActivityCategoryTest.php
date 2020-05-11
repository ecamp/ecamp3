<?php

namespace eCamp\CoreTest\Entity;

use eCamp\Core\Entity\ActivityCategory;
use eCamp\Core\Entity\ActivityType;
use eCamp\Core\Entity\Camp;
use eCamp\LibTest\PHPUnit\AbstractTestCase;

/**
 * @internal
 */
class ActivityCategoryTest extends AbstractTestCase {
    public function testActivityCategory() {
        $camp = new Camp();

        $activityType = new ActivityType();
        $activityType->setDefaultColor('#1fa2df');
        $activityType->setDefaultNumberingStyle('i');

        $activityCategory = new ActivityCategory();
        $activityCategory->setActivityType($activityType);
        $activityCategory->setCamp($camp);
        $activityCategory->setName('TestCategory');
        $activityCategory->setShort('TC');

        $this->assertEquals($activityType, $activityCategory->getActivityType());
        $this->assertEquals($camp, $activityCategory->getCamp());
        $this->assertEquals('TestCategory', $activityCategory->getName());
        $this->assertEquals('TC', $activityCategory->getShort());
        $this->assertEquals('#1fa2df', $activityCategory->getColor());
        $activityCategory->setColor('#FF00FF');
        $this->assertEquals('#FF00FF', $activityCategory->getColor());
        $this->assertEquals('i', $activityCategory->getNumberingStyle());
    }

    public function testNumberingStyle() {
        $activityCategory = new ActivityCategory();

        $this->assertEquals('31', $activityCategory->getStyledNumber(31));

        $activityCategory->setNumberingStyle('a');
        $this->assertEquals('ae', $activityCategory->getStyledNumber(31));

        $activityCategory->setNumberingStyle('A');
        $this->assertEquals('AE', $activityCategory->getStyledNumber(31));

        $activityCategory->setNumberingStyle('i');
        $this->assertEquals('xxxi', $activityCategory->getStyledNumber(31));

        $activityCategory->setNumberingStyle('I');
        $this->assertEquals('XXXI', $activityCategory->getStyledNumber(31));
    }
}
