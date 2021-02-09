<?php

namespace eCamp\CoreTest\Hydrator;

use eCamp\Core\Entity\CategoryTemplate;
use eCamp\Core\Hydrator\CategoryTemplateHydrator;
use eCamp\LibTest\PHPUnit\AbstractTestCase;

/**
 * @internal
 */
class CategoryTemplateHydratorTest extends AbstractTestCase {
    public function testExtract() {
        $categoryTemplate = new CategoryTemplate();
        $categoryTemplate->setShort('n');
        $categoryTemplate->setName('name');
        $categoryTemplate->setColor('#ff0000');
        $categoryTemplate->setNumberingStyle('i');

        $hydrator = new CategoryTemplateHydrator();
        $data = $hydrator->extract($categoryTemplate);

        $this->assertEquals('n', $data['short']);
        $this->assertEquals('name', $data['name']);
        $this->assertEquals('#ff0000', $data['color']);
        $this->assertEquals('i', $data['numberingStyle']);
    }

    public function testHydrate() {
        $categoryTemplate = new CategoryTemplate();
        $data = [
            'short' => 'n',
            'name' => 'name',
            'color' => '#00ff00',
            'numberingStyle' => 'a',
        ];

        $hydrator = new CategoryTemplateHydrator();
        $hydrator->hydrate($data, $categoryTemplate);

        $this->assertEquals('n', $categoryTemplate->getShort());
        $this->assertEquals('name', $categoryTemplate->getName());
        $this->assertEquals('#00ff00', $categoryTemplate->getColor());
        $this->assertEquals('a', $categoryTemplate->getNumberingStyle());
    }
}
