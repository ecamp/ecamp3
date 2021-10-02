<?php

namespace App\Tests\Api\ColumnLayouts;

use App\Entity\ColumnLayout;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class ReadColumnLayoutTest extends ECampApiTestCase {
    // TODO security tests when not logged in or not collaborator

    public function testGetSingleColumnLayoutIsAllowedForCollaborator() {
        /** @var ColumnLayout $contentNode */
        $contentNode = static::$fixtures['columnLayoutChild1'];
        static::createClientWithCredentials()->request('GET', '/content_nodes/'.$contentNode->getId());
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $contentNode->getId(),
            'instanceName' => $contentNode->instanceName,
            'slot' => $contentNode->slot,
            'position' => $contentNode->position,
            'contentTypeName' => $contentNode->getContentTypeName(),
            'jsonConfig' => $contentNode->jsonConfig,
            '_links' => [
                'parent' => ['href' => $this->getIriFor($contentNode->parent)],
                'owner' => ['href' => $this->getIriFor('activity1')],
                'ownerCategory' => ['href' => $this->getIriFor('category1')],
                'children' => ['href' => '/content_nodes?parent='.$this->getIriFor('columnLayoutChild1')],
            ],
        ]);
    }
}
