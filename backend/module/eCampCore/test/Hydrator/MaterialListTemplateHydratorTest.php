<?php

namespace eCamp\CoreTest\Hydrator;

use eCamp\Core\Entity\MaterialListTemplate;
use eCamp\Core\Hydrator\MaterialListTemplateHydrator;
use eCamp\LibTest\PHPUnit\AbstractTestCase;

/**
 * @internal
 */
class MaterialListTemplateHydratorTest extends AbstractTestCase {
    public function testExtract() {
        $materialListTemplate = new MaterialListTemplate();
        $materialListTemplate->setName('name');

        $hydrator = new MaterialListTemplateHydrator();
        $data = $hydrator->extract($materialListTemplate);

        $this->assertEquals('name', $data['name']);
    }

    public function testHydrate() {
        $materialListTemplate = new MaterialListTemplate();
        $data = [
            'name' => 'name',
        ];

        $hydrator = new MaterialListTemplateHydrator();
        $hydrator->hydrate($data, $materialListTemplate);

        $this->assertEquals('name', $materialListTemplate->getName());
    }
}
