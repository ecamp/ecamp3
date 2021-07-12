<?php

namespace App\Tests\Api\MaterialItems;

use App\Entity\MaterialItem;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class ReadMaterialItemTest extends ECampApiTestCase {
    // TODO security tests when not logged in or not collaborator

    public function testGetSingleMaterialItemIsAllowedForCollaborator() {
        /** @var MaterialItem $materialItem */
        $materialItem = static::$fixtures['materialItem1'];
        static::createClientWithCredentials()->request('GET', '/material_items/'.$materialItem->getId());
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $materialItem->getId(),
            'quantity' => (int) $materialItem->quantity,
            'unit' => $materialItem->unit,
            'article' => $materialItem->article,
            '_links' => [
                //'period' => null,
                'materialList' => ['href' => $this->getIriFor('materialList1')],
                'contentNode' => ['href' => $this->getIriFor('contentNode1')],
            ],
        ]);
    }
}
