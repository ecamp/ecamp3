<?php

namespace App\Tests\Api\ContentNodes\MultiSelect;

use App\Entity\ContentNode\MultiSelect;
use App\Entity\ContentNode\MultiSelectOption;
use App\Tests\Api\ContentNodes\ReadContentNodeTestCase;

/**
 * @internal
 */
class ReadMultiSelectTest extends ReadContentNodeTestCase {
    public function setUp(): void {
        parent::setUp();

        $this->endpoint = '/content_node/multi_selects';
        $this->defaultEntity = static::$fixtures['multiSelect1'];
    }

    public function testGetMultiSelect() {
        // given
        /** @var MultiSelect $contentNode */
        $multiSelect = $this->defaultEntity;

        /** @var MultiSelectOption $multiSelectOption */
        $multiSelectOption = static::$fixtures['multiSelectOption1'];

        // when
        $this->get($multiSelect);

        // then
        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonContains([
            '_links' => [
                'options' => ['href' => '/content_node/multi_select_options?multiSelect='.urlencode($this->getIriFor($multiSelect))],
            ],
            '_embedded' => [
                'options' => [
                    [
                        'translateKey' => $multiSelectOption->translateKey,
                        'checked' => $multiSelectOption->checked,
                        'position' => $multiSelectOption->getPosition(),
                        'id' => $multiSelectOption->getId(),
                        '_links' => [
                            'multiSelect' => ['href' => $this->getIriFor($multiSelect)],
                        ],
                    ],
                ],
            ],
        ]);
    }
}
