<?php

namespace App\Tests\Api\ColumnLayouts;

use App\Entity\ContentNode\ColumnLayout;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class DeleteColumnLayoutTest extends ECampApiTestCase {
    // TODO security tests when not logged in or not collaborator

    public function testDeleteColumnLayoutIsAllowedForCollaborator() {
        $contentNode = static::$fixtures['columnLayout2'];
        static::createClientWithCredentials()->request('DELETE', '/content_node/column_layouts/'.$contentNode->getId());
        $this->assertResponseStatusCodeSame(204);
        $this->assertNull($this->getEntityManager()->getRepository(ColumnLayout::class)->find($contentNode->getId()));
    }

    public function testDeleteColumnLayoutIsNotAllowedWhenColumnLayoutIsRoot() {
        //$this->markTestSkipped('To be properly implemented. Currently throws a SQL Error (500)');

        $contentNode = static::$fixtures['columnLayout1'];
        static::createClientWithCredentials()->request('DELETE', '/content_node/column_layouts/'.$contentNode->getId());
        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }
}
