<?php

namespace App\Tests\Api\MaterialLists;

use App\Entity\MaterialList;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class DeleteMaterialListTest extends ECampApiTestCase {
    public function testDeleteMaterialListIsDeniedForAnonymousUser() {
        $materialList = static::$fixtures['materialList1'];
        static::createBasicClient()->request('DELETE', '/material_lists/'.$materialList->getId());
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testDeleteMaterialListIsDeniedForUnrelatedUser() {
        $materialList = static::$fixtures['materialList1'];
        static::createClientWithCredentials(['username' => static::$fixtures['user4unrelated']->getUsername()])
            ->request('DELETE', '/material_lists/'.$materialList->getId())
        ;

        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testDeleteMaterialListIsDeniedForInactiveCollaborator() {
        $materialList = static::$fixtures['materialList1'];
        static::createClientWithCredentials(['username' => static::$fixtures['user5inactive']->getUsername()])
            ->request('DELETE', '/material_lists/'.$materialList->getId())
        ;

        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testDeleteMaterialListIsDeniedForGuest() {
        $materialList = static::$fixtures['materialList1'];
        static::createClientWithCredentials(['username' => static::$fixtures['user3guest']->getUsername()])
            ->request('DELETE', '/material_lists/'.$materialList->getId())
        ;

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testDeleteMaterialListIsAllowedForMember() {
        $materialList = static::$fixtures['materialList1'];
        static::createClientWithCredentials(['username' => static::$fixtures['user2member']->getUsername()])
            ->request('DELETE', '/material_lists/'.$materialList->getId())
        ;
        $this->assertResponseStatusCodeSame(204);
        $this->assertNull($this->getEntityManager()->getRepository(MaterialList::class)->find($materialList->getId()));
    }

    public function testDeleteMaterialListIsAllowedForManager() {
        $materialList = static::$fixtures['materialList1'];
        static::createClientWithCredentials()->request('DELETE', '/material_lists/'.$materialList->getId());
        $this->assertResponseStatusCodeSame(204);
        $this->assertNull($this->getEntityManager()->getRepository(MaterialList::class)->find($materialList->getId()));
    }

    public function testDeleteMaterialListFromCampPrototypeIsDeniedForUnrelatedUser() {
        $materialList = static::$fixtures['materialList1campPrototype'];
        static::createClientWithCredentials()->request('DELETE', '/material_lists/'.$materialList->getId());

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }
}
