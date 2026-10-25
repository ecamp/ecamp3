<?php

namespace App\Tests\Api\ContentNodes\NoData;

use App\Tests\Api\ContentNodes\ReadContentNodeTestCase;

/**
 * @internal
 */
class ReadNoDataTest extends ReadContentNodeTestCase {
    #[\Override]
    public function setUp(): void {
        parent::setUp();

        $this->endpoint = '/content_node/no_datas';
        $this->defaultEntity = static::getFixture('noData1');
        $this->campPrototypeEntity = static::getFixture('noDataCampPrototype');
        $this->sharedCampEntity = static::getFixture('noDataCampShared');
    }
}
