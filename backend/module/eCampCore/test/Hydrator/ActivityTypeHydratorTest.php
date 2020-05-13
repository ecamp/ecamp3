<?php

namespace eCamp\CoreTest\Hydrator;

use eCamp\Core\Entity\ActivityType;
use eCamp\Core\Hydrator\ActivityTypeHydrator;
use eCamp\LibTest\PHPUnit\AbstractTestCase;

/**
 * @internal
 */
class ActivityTypeHydratorTest extends AbstractTestCase {
    public function testExtract() {
        $activityType = new ActivityType();
        $activityType->setName('name');
        $activityType->setDefaultColor('#ff0000');
        $activityType->setDefaultNumberingStyle('i');

        $hydrator = new ActivityTypeHydrator();
        $data = $hydrator->extract($activityType);

        $this->assertEquals('name', $data['name']);
        $this->assertEquals('#ff0000', $data['defaultColor']);
        $this->assertEquals('i', $data['defaultNumberingStyle']);
    }

    public function testHydrate() {
        $activityType = new ActivityType();
        $data = [
            'name' => 'name',
            'defaultColor' => '#00ff00',
            'defaultNumberingStyle' => 'a',
        ];

        $hydrator = new ActivityTypeHydrator();
        $hydrator->hydrate($data, $activityType);

        $this->assertEquals('name', $activityType->getName());
        $this->assertEquals('#00ff00', $activityType->getDefaultColor());
        $this->assertEquals('a', $activityType->getDefaultNumberingStyle());
    }
}
