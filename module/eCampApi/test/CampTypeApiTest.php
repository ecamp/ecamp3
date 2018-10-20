<?php

namespace eCamp\ApiTest;

use eCamp\Core\Entity\CampType;

class CampTypeApiTest extends AbstractApiTestCase {
    function setUp() {
        parent::setUp();
        $this->createDummyData();
    }

    public function testFetchAllCampTypes() {
        /** @var CampType $campType */
        $campType = $this->getRandomEntity(CampType::class);

        $this->dispatchGet("/api/camp_type");
        $json = $this->getResponseJson();

        $items = $json->_embedded->items;
        $campTypeIds = array_map(function($i) {
            return $i->id;
        }, $items);

        $this->assertContains($campType->getId(), $campTypeIds);
    }
}