<?php

namespace App\Tests\Api\ContentNodes\MultiSelect\Option;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class DeleteMultiSelectOptionTest extends ECampApiTestCase {
    public function setUp(): void {
        parent::setUp();

        $this->endpoint = '/content_node/multi_select_options';
        $this->defaultEntity = static::$fixtures['multiSelectOption1'];
    }

    public function testDeleteMethodIsNotAllowed() {
        // when
        $this->delete(user: static::$fixtures['user1manager']);

        // then
        $this->assertResponseStatusCodeSame(405);
    }
}
