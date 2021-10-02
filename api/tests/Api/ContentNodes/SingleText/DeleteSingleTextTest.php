<?php

namespace App\Tests\Api\ContentNodes\SingleText;

use App\Entity\ContentNode\SingleText;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class DeleteSingleTextTest extends ECampApiTestCase {
    // TODO security tests when not logged in or not collaborator

    public function testDeleteSingleTextIsAllowedForCollaborator() {
        $contentNode = static::$fixtures['contentNodeChild2'];
        static::createClientWithCredentials()->request('DELETE', '/content_node/single_texts/'.$contentNode->getId());
        $this->assertResponseStatusCodeSame(204);
        $this->assertNull($this->getEntityManager()->getRepository(SingleText::class)->find($contentNode->getId()));
    }
}
