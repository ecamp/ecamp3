<?php

namespace App\Tests\Api\ContentNodes\NoData;

use App\Entity\ContentNode\NoData;
use App\Tests\Api\ContentNodes\CreateContentNodeTestCase;

/**
 * @internal
 */
class CreateNoDataTest extends CreateContentNodeTestCase {
    #[\Override]
    public function setUp(): void {
        parent::setUp();

        $this->endpoint = '/content_node/no_datas';
        $this->entityClass = NoData::class;
        $this->defaultContentType = static::getFixture('contentTypeYSPhysicalActivityDuration');
    }

    public function testCreateNoDataStoresNoData() {
        // when
        $this->create($this->getExampleWritePayload());

        // then
        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains([
            'data' => null,
        ]);
    }

    public function testCreateDoesNotAcceptData() {
        $this->create($this->getExampleWritePayload([
            'data' => [
                'minutes' => 60,
            ],
        ]));

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

    #[\Override]
    protected function getExampleWritePayload($attributes = [], $except = []) {
        return parent::getExampleWritePayload(
            array_merge([
                'data' => null,
            ], $attributes),
            $except
        );
    }
}
