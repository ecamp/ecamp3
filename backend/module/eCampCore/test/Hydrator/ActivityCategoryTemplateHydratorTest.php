<?php

namespace eCamp\CoreTest\Hydrator;

use eCamp\Core\Entity\ActivityCategoryTemplate;
use eCamp\Core\Hydrator\ActivityCategoryTemplateHydrator;
use eCamp\LibTest\PHPUnit\AbstractTestCase;

/**
 * @internal
 */
class ActivityCategoryTemplateHydratorTest extends AbstractTestCase {
    public function testExtract() {
        $activityCategoryTemplate = new ActivityCategoryTemplate();
        $activityCategoryTemplate->setShort('n');
        $activityCategoryTemplate->setName('name');
        $activityCategoryTemplate->setColor('#ff0000');
        $activityCategoryTemplate->setNumberingStyle('i');

        $hydrator = new ActivityCategoryTemplateHydrator();
        $data = $hydrator->extract($activityCategoryTemplate);

        $this->assertEquals('n', $data['short']);
        $this->assertEquals('name', $data['name']);
        $this->assertEquals('#ff0000', $data['color']);
        $this->assertEquals('i', $data['numberingStyle']);
    }

    public function testHydrate() {
        $activityCategoryTemplate = new ActivityCategoryTemplate();
        $data = [
            'short' => 'n',
            'name' => 'name',
            'color' => '#00ff00',
            'numberingStyle' => 'a',
        ];

        $hydrator = new ActivityCategoryTemplateHydrator();
        $hydrator->hydrate($data, $activityCategoryTemplate);

        $this->assertEquals('n', $activityCategoryTemplate->getShort());
        $this->assertEquals('name', $activityCategoryTemplate->getName());
        $this->assertEquals('#00ff00', $activityCategoryTemplate->getColor());
        $this->assertEquals('a', $activityCategoryTemplate->getNumberingStyle());
    }
}
