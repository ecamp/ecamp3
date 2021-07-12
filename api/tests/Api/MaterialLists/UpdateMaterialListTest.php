<?php

namespace App\Tests\Api\MaterialLists;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class UpdateMaterialListTest extends ECampApiTestCase {
    // TODO security tests when not logged in or not collaborator
    // TODO input filter tests
    // TODO validation tests

    public function testPatchMaterialListIsAllowedForCollaborator() {
        $materialList = static::$fixtures['materialList1'];
        static::createClientWithCredentials()->request('PATCH', '/material_lists/'.$materialList->getId(), ['json' => [
            'name' => 'Something',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'name' => 'Something',
        ]);
    }

    public function testPatchMaterialListDisallowsChangingCamp() {
        $materialList = static::$fixtures['materialList1'];
        static::createClientWithCredentials()->request('PATCH', '/material_lists/'.$materialList->getId(), ['json' => [
            'camp' => $this->getIriFor('camp2'),
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'detail' => 'Extra attributes are not allowed ("camp" are unknown).',
        ]);
    }

    public function testPatchMaterialItemValidatesMissingArticle() {
        $materialList = static::$fixtures['materialList1'];
        static::createClientWithCredentials()->request('PATCH', '/material_lists/'.$materialList->getId(), ['json' => [
            'name' => null,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'detail' => 'The type of the "name" attribute must be "string", "NULL" given.',
        ]);
    }
}
