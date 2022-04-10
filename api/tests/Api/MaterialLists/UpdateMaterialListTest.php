<?php

namespace App\Tests\Api\MaterialLists;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class UpdateMaterialListTest extends ECampApiTestCase {
    // TODO input filter tests
    // TODO validation tests

    public function testPatchMaterialListIsDeniedForAnonymousUser() {
        $materialList = static::$fixtures['materialList1'];
        static::createBasicClient()->request('PATCH', '/material_lists/'.$materialList->getId(), ['json' => [
            'name' => 'Something',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testPatchMaterialListIsDeniedForUnrelatedUser() {
        $materialList = static::$fixtures['materialList1'];
        static::createClientWithCredentials(['username' => static::$fixtures['user4unrelated']->getUsername()])
            ->request('PATCH', '/material_lists/'.$materialList->getId(), ['json' => [
                'name' => 'Something',
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;
        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testPatchMaterialListIsDeniedForInactiveCollaborator() {
        $materialList = static::$fixtures['materialList1'];
        static::createClientWithCredentials(['username' => static::$fixtures['user5inactive']->getUsername()])
            ->request('PATCH', '/material_lists/'.$materialList->getId(), ['json' => [
                'name' => 'Something',
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;
        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testPatchMaterialListIsDeniedForGuest() {
        $materialList = static::$fixtures['materialList1'];
        static::createClientWithCredentials(['username' => static::$fixtures['user3guest']->getUsername()])
            ->request('PATCH', '/material_lists/'.$materialList->getId(), ['json' => [
                'name' => 'Something',
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;
        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testPatchMaterialListIsAllowedForMember() {
        $materialList = static::$fixtures['materialList1'];
        static::createClientWithCredentials(['username' => static::$fixtures['user2member']->getUsername()])
            ->request('PATCH', '/material_lists/'.$materialList->getId(), ['json' => [
                'name' => 'Something',
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'name' => 'Something',
        ]);
    }

    public function testPatchMaterialListIsAllowedForManager() {
        $materialList = static::$fixtures['materialList1'];
        static::createClientWithCredentials()->request('PATCH', '/material_lists/'.$materialList->getId(), ['json' => [
            'name' => 'Something',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'name' => 'Something',
        ]);
    }

    public function testSetNameOfMaterialListWithCampCollaborationOverwritesGeneratedName() {
        $materialList = static::$fixtures['materialList3Manager'];
        static::createClientWithCredentials()->request('PATCH', '/material_lists/'.$materialList->getId(), ['json' => [
            'name' => 'Something',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'name' => 'Something',
        ]);
    }

    public function testPatchMaterialListInCampPrototypeIsDeniedForUnrelatedUser() {
        $materialList = static::$fixtures['materialList1campPrototype'];
        static::createClientWithCredentials()->request('PATCH', '/material_lists/'.$materialList->getId(), ['json' => [
            'name' => 'Something',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testPatchMaterialListDisallowsChangingCamp() {
        $materialList = static::$fixtures['materialList1'];
        static::createClientWithCredentials()->request('PATCH', '/material_lists/'.$materialList->getId(), ['json' => [
            'camp' => $this->getIriFor('camp2'),
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'detail' => 'Extra attributes are not allowed ("camp" is unknown).',
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
