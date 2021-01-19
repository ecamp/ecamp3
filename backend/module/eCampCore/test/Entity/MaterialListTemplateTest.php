<?php

namespace eCamp\CoreTest\Entity;

use eCamp\Core\Entity\CampTemplate;
use eCamp\Core\Entity\MaterialListTemplate;
use eCamp\LibTest\PHPUnit\AbstractTestCase;

/**
 * @internal
 */
class MaterialListTemplateTest extends AbstractTestCase {
    public function testMaterialListTemplate() {
        $materialListTemplate = new MaterialListTemplate();
        $campTemplate = new CampTemplate();

        $materialListTemplate->setName('name');
        $materialListTemplate->setCampTemplate($campTemplate);

        $this->assertEquals('name', $materialListTemplate->getName());
        $this->assertEquals($campTemplate, $materialListTemplate->getCampTemplate());
    }
}
