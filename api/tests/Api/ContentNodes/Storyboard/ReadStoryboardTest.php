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

        $this->endpoint = '/content_node/storyboards';
        $this->defaultEntity = static::$fixtures['storyboard1'];
    }

    public function testGetStoryboard() {
        // given
        /** @var Storyboard $contentNode */
        $storyboard = $this->defaultEntity;

        /** @var StoryboardSection $storyboardSection */
        $storyboardSection = static::$fixtures['storyboardSection1'];

        // when
        $this->get($storyboard);

        // then
        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonContains([
            '_links' => [
                'sections' => ['href' => '/content_node/storyboard_sections?storyboard='.urlencode($this->getIriFor($storyboard))],
            ],
            '_embedded' => [
                'sections' => [
                    [
                        'column1' => $storyboardSection->column1,
                        'column2' => $storyboardSection->column2,
                        'column3' => $storyboardSection->column3,
                        'position' => $storyboardSection->getPosition(),
                        'id' => $storyboardSection->getId(),
                        '_links' => [
                            'storyboard' => ['href' => $this->getIriFor($storyboard)],
                        ],
                    ],
                ],
            ],
        ]);
    }
}
