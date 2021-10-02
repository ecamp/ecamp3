<?php

namespace App\Tests\Api\ContentNodes\SingleText;

use App\Entity\ContentNode;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class ReadSingleTextTest extends ECampApiTestCase {
    // TODO security tests when not logged in or not collaborator

    public function testGetSingleTextIsAllowedForCollaborator() {
        /** @var ContentNode $contentNode */
        $contentNode = static::$fixtures['singleText1'];
        static::createClientWithCredentials()->request('GET', '/content_node/single_texts/'.$contentNode->getId());
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $contentNode->getId(),
            'instanceName' => $contentNode->instanceName,
            'slot' => $contentNode->slot,
            'position' => $contentNode->position,
            'contentTypeName' => $contentNode->getContentTypeName(),
            'jsonConfig' => $contentNode->jsonConfig,
            'text' => $contentNode->text,
            '_links' => [
                'parent' => ['href' => $this->getIriFor($contentNode->parent)],
                'owner' => ['href' => $this->getIriFor('activity1')],
                'ownerCategory' => ['href' => $this->getIriFor('category1')],
            ],
        ]);
    }
}
