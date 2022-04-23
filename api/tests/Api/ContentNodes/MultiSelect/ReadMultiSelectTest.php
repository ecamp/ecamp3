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

        /** @var MultiSelectOption $multiSelectOption1 */
        $multiSelectOption1 = static::$fixtures['multiSelectOption1'];

        /** @var MultiSelectOption $multiSelectOption2 */
        $multiSelectOption2 = static::$fixtures['multiSelectOption2'];

        // when
        $this->get($multiSelect);

        // then
        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonContains([
            '_links' => [
                'options' => ['href' => '/content_node/multi_select_options?multiSelect='.$this->getIriFor($multiSelect)],
            ],
            '_embedded' => [
                'options' => [
                    [
                        'translateKey' => $multiSelectOption2->translateKey,
                        'checked' => $multiSelectOption2->checked,
                        'position' => $multiSelectOption2->getPosition(),
                        'id' => $multiSelectOption2->getId(),
                        '_links' => [
                            'multiSelect' => ['href' => $this->getIriFor($multiSelect)],
                        ],
                    ],
                    [
                        'translateKey' => $multiSelectOption1->translateKey,
                        'checked' => $multiSelectOption1->checked,
                        'position' => $multiSelectOption1->getPosition(),
                        'id' => $multiSelectOption1->getId(),
                        '_links' => [
                            'multiSelect' => ['href' => $this->getIriFor($multiSelect)],
                        ],
                    ],
                ],
            ],
        ]);
    }
}
