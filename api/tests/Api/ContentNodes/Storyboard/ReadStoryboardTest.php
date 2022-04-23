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

        /** @var StoryboardSection $storyboardSection1 */
        $storyboardSection1 = static::$fixtures['storyboardSection1'];

        /** @var StoryboardSection $storyboardSection2 */
        $storyboardSection2 = static::$fixtures['storyboardSection2'];

        // when
        $this->get($storyboard);

        // then
        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonContains([
            '_links' => [
                'sections' => ['href' => '/content_node/storyboard_sections?storyboard='.$this->getIriFor($storyboard)],
            ],
            '_embedded' => [
                'sections' => [
                    [
                        'column1' => $storyboardSection1->column1,
                        'column2' => $storyboardSection1->column2,
                        'column3' => $storyboardSection1->column3,
                        'position' => $storyboardSection1->getPosition(),
                        'id' => $storyboardSection1->getId(),
                        '_links' => [
                            'storyboard' => ['href' => $this->getIriFor($storyboard)],
                        ],
                    ],
                    [
                        'column1' => $storyboardSection2->column1,
                        'column2' => $storyboardSection2->column2,
                        'column3' => $storyboardSection2->column3,
                        'position' => $storyboardSection2->getPosition(),
                        'id' => $storyboardSection2->getId(),
                        '_links' => [
                            'storyboard' => ['href' => $this->getIriFor($storyboard)],
                        ],
                    ],
                ],
            ],
        ]);
    }
}
