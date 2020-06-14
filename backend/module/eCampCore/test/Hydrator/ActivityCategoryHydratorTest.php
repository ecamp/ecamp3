<?php

namespace eCamp\CoreTest\Hydrator;

use eCamp\Core\Entity\ActivityCategory;
use eCamp\Core\Entity\ActivityType;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Hydrator\ActivityCategoryHydrator;
use eCamp\LibTest\PHPUnit\AbstractTestCase;

/**
 * @internal
 */
class ActivityCategoryHydratorTest extends AbstractTestCase {
    public function testExtract() {
        $activityType = new ActivityType();
        $camp = new Camp();
        $activityCategory = new ActivityCategory();
        $activityCategory->setActivityType($activityType);
        $activityCategory->setCamp($camp);
        $activityCategory->setShort('sh');
        $activityCategory->setName('name');
        $activityCategory->setColor('#ff0000');
        $activityCategory->setNumberingStyle('i');

        $hydrator = new ActivityCategoryHydrator();
        $data = $hydrator->extract($activityCategory);

//        $this->assertEquals($activityType, $data['activityType']);
//        $this->assertEquals($camp, $data['camp']);
        $this->assertEquals('sh', $data['short']);
        $this->assertEquals('name', $data['name']);
        $this->assertEquals('#ff0000', $data['color']);
        $this->assertEquals('i', $data['numberingStyle']);
    }

    public function testHydrate() {
        $activityType = new ActivityType();
        $camp = new Camp();

        $activityCategory = new ActivityCategory();
        $data = [
            'short' => 'sh',
            'name' => 'name',
            'color' => '#00ff00',
            'numberingStyle' => 'a',
        ];

        $activityCategory->setCamp($camp);
        $activityCategory->setActivityType($activityType);

        $hydrator = new ActivityCategoryHydrator();
        $hydrator->hydrate($data, $activityCategory);

        $this->assertEquals($activityType, $activityCategory->getActivityType());
        $this->assertEquals($camp, $activityCategory->getCamp());
        $this->assertEquals('sh', $activityCategory->getShort());
        $this->assertEquals('name', $activityCategory->getName());
        $this->assertEquals('#00ff00', $activityCategory->getColor());
        $this->assertEquals('a', $activityCategory->getNumberingStyle());
    }
}
