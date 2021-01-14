<?php

namespace eCamp\CoreTest\Entity;

use eCamp\Core\Entity\ActivityCategory;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\ContentTypeConfig;
use eCamp\LibTest\PHPUnit\AbstractTestCase;

/**
 * @internal
 */
class ActivityCategoryTest extends AbstractTestCase {
    public function testActivityCategory() {
        $camp = new Camp();

        $activityCategory = new ActivityCategory();
        $activityCategory->setCamp($camp);
        $activityCategory->setName('TestCategory');
        $activityCategory->setShort('TC');
        $activityCategory->setColor('#1fa2df');
        $activityCategory->setNumberingStyle('i');

        $this->assertEquals($camp, $activityCategory->getCamp());
        $this->assertEquals('TestCategory', $activityCategory->getName());
        $this->assertEquals('TC', $activityCategory->getShort());
        $this->assertEquals('#1fa2df', $activityCategory->getColor());
        $activityCategory->setColor('#FF00FF');
        $this->assertEquals('#FF00FF', $activityCategory->getColor());
        $this->assertEquals('i', $activityCategory->getNumberingStyle());
    }

    public function testAddRemoveContentTypeConfig() {
        $activityCategory = new ActivityCategory();
        $contentTypeConfig = new ContentTypeConfig();

        $this->assertCount(0, $activityCategory->getContentTypeConfigs());

        $activityCategory->addContentTypeConfig($contentTypeConfig);
        $this->assertCount(1, $activityCategory->getContentTypeConfigs());

        $activityCategory->removeContentTypeConfig($contentTypeConfig);
        $this->assertCount(0, $activityCategory->getContentTypeConfigs());
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
