<?php

namespace App\Tests\Api\ContentNodes\MultiSelect\Option;

use App\Entity\ContentNode\MultiSelect;
use App\Entity\ContentNode\MultiSelectOption;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class CreateMultiSelectOptionTest extends ECampApiTestCase {
    protected MultiSelect $defaultMultiSelect;

    public function setUp(): void {
        parent::setUp();

        $this->endpoint = '/content_node/multi_select_options';
        $this->entityClass = MultiSelectOption::class;

        $this->defaultMultiSelect = static::$fixtures['multiSelect1'];
    }

    public function testCreateMethodIsNotAllowed() {
        // when
        $this->create(user: static::$fixtures['user1manager']);

        // then
        $this->assertResponseStatusCodeSame(405);
    }

    // Payload setup.
    protected function getExampleWritePayload($attributes = [], $except = []) {
        return [];
    }
}
