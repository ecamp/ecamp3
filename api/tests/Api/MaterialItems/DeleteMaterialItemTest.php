<?php

namespace App\Tests\Api\MaterialItems;

use App\Entity\MaterialItem;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class DeleteMaterialItemTest extends ECampApiTestCase {
    // TODO security tests when not logged in or not collaborator

    public function testDeleteMaterialItemIsAllowedForCollaborator() {
        $materialItem = static::$fixtures['materialItem1'];
        static::createClientWithCredentials()->request('DELETE', '/material_items/'.$materialItem->getId());
        $this->assertResponseStatusCodeSame(204);
        $this->assertNull($this->getEntityManager()->getRepository(MaterialItem::class)->find($materialItem->getId()));
    }
}
