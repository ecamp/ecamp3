<?php

namespace eCamp\CoreTest\Entity;

use eCamp\Core\Entity\CampTemplate;
use eCamp\LibTest\PHPUnit\AbstractTestCase;

/**
 * @internal
 */
class CampTemplateTest extends AbstractTestCase {
    public function testCampType() {
        $campTemplate = new CampTemplate();
        $campTemplate->setName('CampType.Name');

        $this->assertEquals('CampType.Name', $campTemplate->getName());
    }
}
