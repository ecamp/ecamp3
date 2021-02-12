<?php

namespace eCamp\CoreTest\Hydrator;

use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\Category;
use eCamp\Core\Hydrator\CategoryHydrator;
use eCamp\LibTest\PHPUnit\AbstractTestCase;

/**
 * @internal
 */
class CategoryHydratorTest extends AbstractTestCase {
    public function testExtract() {
        $camp = new Camp();
        $category = new Category();
        $category->setCamp($camp);
        $category->setShort('sh');
        $category->setName('name');
        $category->setColor('#ff0000');
        $category->setNumberingStyle('i');

        $hydrator = new CategoryHydrator();
        $data = $hydrator->extract($category);

        $this->assertEquals('sh', $data['short']);
        $this->assertEquals('name', $data['name']);
        $this->assertEquals('#ff0000', $data['color']);
        $this->assertEquals('i', $data['numberingStyle']);
    }

    public function testHydrate() {
        $camp = new Camp();

        $category = new Category();
        $data = [
            'short' => 'sh',
            'name' => 'name',
            'color' => '#00ff00',
            'numberingStyle' => 'a',
        ];

        $category->setCamp($camp);

        $hydrator = new CategoryHydrator();
        $hydrator->hydrate($data, $category);

        $this->assertEquals($camp, $category->getCamp());
        $this->assertEquals('sh', $category->getShort());
        $this->assertEquals('name', $category->getName());
        $this->assertEquals('#00ff00', $category->getColor());
        $this->assertEquals('a', $category->getNumberingStyle());
    }
}
