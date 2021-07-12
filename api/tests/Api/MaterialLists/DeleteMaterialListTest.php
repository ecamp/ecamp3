<?php

namespace App\Tests\Api\MaterialLists;

use App\Entity\MaterialList;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class DeleteMaterialListTest extends ECampApiTestCase {
    // TODO security tests when not logged in or not collaborator

    public function testDeleteMaterialListIsAllowedForCollaborator() {
        $materialList = static::$fixtures['materialList1'];
        static::createClientWithCredentials()->request('DELETE', '/material_lists/'.$materialList->getId());
        $this->assertResponseStatusCodeSame(204);
        $this->assertNull($this->getEntityManager()->getRepository(MaterialList::class)->find($materialList->getId()));
    }
}
