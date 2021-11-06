<?php

namespace App\Tests\Api\ContentNodes\Storyboard;

use App\Tests\Api\ContentNodes\UpdateContentNodeTestCase;

/**
 * @internal
 */
class UpdateStoryboardTest extends UpdateContentNodeTestCase {
    public function setUp(): void {
        parent::setUp();

        $this->endpoint = '/content_node/storyboards';
        $this->defaultEntity = static::$fixtures['storyboard1'];
    }

    public function testPatchEmbeddedSections() {
        // given

        /** @var StoryboardSection $storyboardSection1 */
        $storyboardSection1 = static::$fixtures['storyboardSection1'];

        /** @var StoryboardSection $storyboardSection2 */
        $storyboardSection2 = static::$fixtures['storyboardSection2'];

        // when
        $this->patch($this->defaultEntity, [
            'sections' => [
                [
                    'id' => $this->getIriFor($storyboardSection1),
                    'column1' => 'test123',
                ],
                [
                    'id' => $this->getIriFor($storyboardSection2),
                    'column1' => 'testABC',
                ],
                [
                    'column1' => 'newSection.column1',
                ],
            ],
        ]);

        // then
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            '_embedded' => [
                'sections' => [
                    [
                        'column1' => 'newSection.column1',
                        'column2' => null,
                        'column3' => null,
                    ],
                    [
                        'column1' => 'test123',
                        'column2' => $storyboardSection1->column2,
                        'column3' => $storyboardSection1->column3,
                        'pos' => $storyboardSection1->getPos(),
                        'id' => $storyboardSection1->getId(),
                        '_links' => [
                            'storyboard' => ['href' => $this->getIriFor($this->defaultEntity)],
                        ],
                    ],
                    [
                        'column1' => 'testABC',
                        'column2' => $storyboardSection2->column2,
                        'column3' => $storyboardSection2->column3,
                        'pos' => $storyboardSection2->getPos(),
                        'id' => $storyboardSection2->getId(),
                        '_links' => [
                            'storyboard' => ['href' => $this->getIriFor($this->defaultEntity)],
                        ],
                    ],
                ],
            ],
        ]);
    }
}
