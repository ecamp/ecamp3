<?php

namespace App\Tests\Api\MaterialLists;

use App\Entity\MaterialList;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class ReadMaterialListTest extends ECampApiTestCase {
    // TODO security tests when not logged in or not collaborator

    public function testGetSingleMaterialListIsAllowedForCollaborator() {
        /** @var MaterialList $materialList */
        $materialList = static::$fixtures['materialList1'];
        static::createClientWithCredentials()->request('GET', '/material_lists/'.$materialList->getId());
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $materialList->getId(),
            'name' => $materialList->name,
            '_links' => [
                'camp' => ['href' => $this->getIriFor('camp1')],
                'materialItems' => ['href' => '/material_items?materialList=/material_lists/'.$materialList->getId()],
            ],
        ]);
    }
}
