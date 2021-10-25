<?php

namespace App\Tests\Api\ContentNodes\Storyboard;

use App\Entity\ContentNode\Storyboard;
use App\Tests\Api\ContentNodes\CreateContentNodeTestCase;

/**
 * @internal
 */
class CreateStoryboardTest extends CreateContentNodeTestCase {
    public function setUp(): void {
        parent::setUp();

        $this->endpoint = 'storyboards';
        $this->contentNodeClass = Storyboard::class;
        $this->defaultContentType = static::$fixtures['contentTypeStoryboard'];
    }

    public function testCreateStoryboardFromPrototype() {
        // given
        $prototype = static::$fixtures['storyboard1'];
        $prototypeSection = static::$fixtures['storyboardSection1'];

        // when
        $response = $this->create($this->getExampleWritePayload(['prototype' => $this->getIriFor('storyboard1')]));

        // then
        $this->assertResponseStatusCodeSame(201);
        $this->assertCount(1, $response->toArray()['_links']['sections']);
        $this->assertJsonContains([
            '_embedded' => [
                'sections' => [
                    [
                        'column1' => $prototypeSection->column1,
                        'column2' => $prototypeSection->column2,
                        'column3' => $prototypeSection->column3,
                    ],
                ],
            ],
        ]);
    }
}
