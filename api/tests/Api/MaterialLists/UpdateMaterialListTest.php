<?php

namespace App\Tests\Api\MaterialLists;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class UpdateMaterialListTest extends ECampApiTestCase {
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

    public function testPatchMaterialListDisallowsChangingCampCollaboration() {
        $materialList = static::$fixtures['materialList3Manager'];
        static::createClientWithCredentials()->request('PATCH', '/material_lists/'.$materialList->getId(), ['json' => [
            'campCollaboration' => $this->getIriFor('campCollaboration2member'),
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'detail' => 'Extra attributes are not allowed ("campCollaboration" is unknown).',
        ]);
    }

    public function testPatchMaterialListValidatesMissingName() {
        $materialList = static::$fixtures['materialList1'];
        static::createClientWithCredentials()->request('PATCH', '/material_lists/'.$materialList->getId(), ['json' => [
            'name' => null,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'name',
                    'message' => 'This value should not be blank.',
                ],
            ],
        ]);
    }

    public function testPatchMaterialListValidatesTooLongName() {
        $materialList = static::$fixtures['materialList1'];
        static::createClientWithCredentials()->request('PATCH', '/material_lists/'.$materialList->getId(), ['json' => [
            'name' => 'a very long name with more than a',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'name',
                    'message' => 'This value is too long. It should have 32 characters or less.',
                ],
            ],
        ]);
    }

    public function testPatchMaterialListTrimsNameBeforeValidatingLength() {
        $materialList = static::$fixtures['materialList1'];
        static::createClientWithCredentials(['username' => static::$fixtures['user2member']->getUsername()])
            ->request('PATCH', '/material_lists/'.$materialList->getId(), ['json' => [
                'name' => 'a very long name with more than  ',
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'name' => 'a very long name with more than',
        ]);
    }

    public function testPatchMaterialListTrimsName() {
        $materialList = static::$fixtures['materialList1'];
        static::createClientWithCredentials(['username' => static::$fixtures['user2member']->getUsername()])
            ->request('PATCH', '/material_lists/'.$materialList->getId(), ['json' => [
                'name' => ' Something ',
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'name' => 'Something',
        ]);
    }

    public function testPatchMaterialListCleansHtml() {
        $materialList = static::$fixtures['materialList1'];
        static::createClientWithCredentials(['username' => static::$fixtures['user2member']->getUsername()])
            ->request('PATCH', '/material_lists/'.$materialList->getId(), ['json' => [
                'name' => ' Some<script>alert(1)</script>thing ',
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'name' => 'Something',
        ]);
    }
}
