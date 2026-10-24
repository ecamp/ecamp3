<?php

namespace App\Tests\Api\ContentNodes\NoData;

use App\Tests\Api\ContentNodes\UpdateContentNodeTestCase;

/**
 * @internal
 */
class UpdateNoDataTest extends UpdateContentNodeTestCase {
    #[\Override]
    public function setUp(): void {
        parent::setUp();

        $this->endpoint = '/content_node/no_datas';
        $this->defaultEntity = static::getFixture('noData1');
        $this->campPrototypeEntity = static::getFixture('noDataCampPrototype');
        $this->sharedCampEntity = static::getFixture('noDataCampShared');
    }

    public function testPatchDoesNotAcceptData() {
        $this->patch($this->defaultEntity, [
            'data' => [
                'minutes' => 60,
            ],
        ]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                0 => [
                    'propertyPath' => 'data',
                    'message' => 'This value should be null.',
                ],
            ],
        ]);
    }
}
