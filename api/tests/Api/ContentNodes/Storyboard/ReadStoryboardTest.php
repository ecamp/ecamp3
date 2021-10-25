<?php

namespace App\Tests\Api\ContentNodes\Storyboard;

use App\Entity\ContentNode\Storyboard;
use App\Entity\ContentNode\StoryboardSection;
use App\Tests\Api\ContentNodes\ReadContentNodeTestCase;

/**
 * @internal
 */
class ReadStoryboardTest extends ReadContentNodeTestCase {
    public function setUp(): void {
        parent::setUp();

        $this->endpoint = 'storyboards';
        $this->defaultContentNode = static::$fixtures['storyboard1'];
    }

    public function testGetStoryboard() {
        // given
        /** @var Storyboard $contentNode */
        $contentNode = $this->defaultContentNode;

        /** @var StoryboardSection $storyboardSection */
        $storyboardSection = static::$fixtures['storyboardSection1'];

        // when
        $this->get($contentNode);

        // then
        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonContains([
            '_links' => [
                'sections' => [
                    ['href' => $this->getIriFor($storyboardSection)],
                ],
            ],
            '_embedded' => [
                'sections' => [
                    [
                        'column1' => $storyboardSection->column1,
                        'column2' => $storyboardSection->column2,
                        'column3' => $storyboardSection->column3,
                        'id' => $storyboardSection->getId(),
                        '_links' => [
                            'storyboard' => ['href' => $this->getIriFor($contentNode)],
                        ],
                    ],
                ],
            ],
        ]);
    }
}
