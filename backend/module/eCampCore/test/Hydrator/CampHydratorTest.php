<?php

namespace eCamp\CoreTest\Hydrator;

use eCamp\Core\Entity\Camp;
use eCamp\Core\Hydrator\CampHydrator;
use eCamp\LibTest\PHPUnit\AbstractTestCase;

/**
 * @internal
 */
class CampHydratorTest extends AbstractTestCase {
    public function testExtract(): void {
        $camp = new Camp();
        $camp->setName('name');
        $camp->setTitle('title');
        $camp->setMotto('motto');
        $camp->setAddressName('AdrName');
        $camp->setAddressStreet('AdrStreet');
        $camp->setAddressZipcode('AdrZipcode');
        $camp->setAddressCity('AdrCity');
        $camp->setIsPrototype(true);

        $hydrator = new CampHydrator();
        $data = $hydrator->extract($camp);

        $this->assertEquals('name', $data['name']);
        $this->assertEquals('title', $data['title']);
        $this->assertEquals('motto', $data['motto']);
        $this->assertEquals('AdrName', $data['addressName']);
        $this->assertEquals('AdrStreet', $data['addressStreet']);
        $this->assertEquals('AdrZipcode', $data['addressZipcode']);
        $this->assertEquals('AdrCity', $data['addressCity']);
        $this->assertTrue($data['isPrototype']);
    }

    public function testHydrate(): void {
        $camp = new Camp();
        $data = [
            'name' => 'name',
            'title' => 'title',
            'motto' => 'motto',
            'addressName' => 'AdrName',
            'addressStreet' => 'AdrStreet',
            'addressZipcode' => 'AdrZipcode',
            'addressCity' => 'AdrCity',
            'isPrototype' => true,
        ];

        $hydrator = new CampHydrator();
        $hydrator->hydrate($data, $camp);

        $this->assertEquals('name', $camp->getName());
        $this->assertEquals('title', $camp->getTitle());
        $this->assertEquals('motto', $camp->getMotto());
        $this->assertEquals('AdrName', $camp->getAddressName());
        $this->assertEquals('AdrStreet', $camp->getAddressStreet());
        $this->assertEquals('AdrZipcode', $camp->getAddressZipcode());
        $this->assertEquals('AdrCity', $camp->getAddressCity());
        $this->assertTrue($camp->getIsPrototype());
    }
}
