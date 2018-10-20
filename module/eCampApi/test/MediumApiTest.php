<?php

namespace eCamp\ApiTest;

use eCamp\Core\Entity\Medium;

class MediumApiTest extends AbstractApiTestCase {
    function setUp() {
        parent::setUp();
        $this->createDummyData();
    }

    public function testFetchAllMedium() {
        /** @var Medium $medium */
        $medium = $this->getRandomEntity(Medium::class);

        $this->dispatchGet("/api/medium");
        $json = $this->getResponseJson();

        $items = $json->_embedded->items;
        $mediumIds = array_map(function($i) {
            return $i->name;
        }, $items);

        $this->assertContains($medium->getName(), $mediumIds);
    }
}